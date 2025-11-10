const express = require('express');
const axios = require('axios');
const cors = require('cors');

const app = express();
const PORT = process.env.PORT || 3000;

// Middleware
app.use(cors({
  origin: '*',
  methods: ['GET', 'POST', 'OPTIONS'],
  allowedHeaders: ['Content-Type', 'Authorization', 'X-Requested-With', 'Accept', 'Origin'],
  credentials: false,
  maxAge: 3600
}));

app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// Health check endpoint
app.get('/', (req, res) => {
  res.json({
    status: 'success',
    message: 'WhatsApp API Proxy Server is running',
    version: '1.0.0',
    timestamp: new Date().toISOString()
  });
});

// Health check endpoint
app.get('/health', (req, res) => {
  res.json({
    status: 'healthy',
    timestamp: new Date().toISOString()
  });
});

// Test endpoint
app.get('/test', (req, res) => {
  res.json({
    status: 'success',
    message: 'API endpoint is working',
    method: req.method,
    timestamp: new Date().toISOString()
  });
});

// WhatsApp API Proxy endpoint
app.post('/api/whatsapp-proxy', async (req, res) => {
  try {
    const { phone_number, message, message_type, location, phone_number_id, api_key } = req.body;

    // Validate required fields
    if (!phone_number || !phone_number_id || !api_key) {
      return res.status(400).json({
        success: false,
        error: 'Missing required fields: phone_number, phone_number_id, api_key'
      });
    }

    // Build payload
    let payload;
    
    if (message_type === 'location' && location) {
      // Validate location fields
      if (!location.latitude || !location.longitude || !location.name || !location.address) {
        return res.status(400).json({
          success: false,
          error: 'Missing location fields: latitude, longitude, name, address'
        });
      }

      payload = {
        messaging_product: 'whatsapp',
        recipient_type: 'individual',
        to: phone_number.trim(),
        type: 'location',
        location: {
          latitude: parseFloat(location.latitude),
          longitude: parseFloat(location.longitude),
          name: location.name,
          address: location.address
        }
      };
    } else {
      // Validate message
      if (!message) {
        return res.status(400).json({
          success: false,
          error: 'Missing message'
        });
      }

      payload = {
        messaging_product: 'whatsapp',
        to: phone_number.trim(),
        type: 'text',
        text: {
          body: message
        }
      };
    }

    // WhatsApp API URL
    const url = `https://waba.xtendonline.com/v3/${phone_number_id.trim()}/messages`;

    // Make request to WhatsApp API
    const response = await axios.post(url, payload, {
      headers: {
        'Content-Type': 'application/json',
        'apikey': api_key.trim()
      },
      timeout: 30000,
      validateStatus: function (status) {
        return status < 500; // Don't throw on 4xx errors
      }
    });

    // Handle response
    if (response.status >= 400) {
      const errorMsg = response.data?.error?.message || 
                      response.data?.error?.code || 
                      'WhatsApp API error';
      
      return res.status(response.status).json({
        success: false,
        error: errorMsg,
        http_code: response.status,
        details: response.data
      });
    }

    // Check for error in response data
    if (response.data?.error) {
      const errorMsg = response.data.error.message || 
                      response.data.error.code || 
                      'WhatsApp API error';
      
      return res.status(400).json({
        success: false,
        error: errorMsg,
        details: response.data
      });
    }

    // Success response
    const messageId = response.data?.messages?.[0]?.id || null;
    const waId = response.data?.contacts?.[0]?.wa_id || null;

    res.status(200).json({
      success: true,
      message_id: messageId,
      phone_number: phone_number.trim(),
      wa_id: waId,
      data: response.data
    });

  } catch (error) {
    console.error('Error in WhatsApp API proxy:', error.message);
    
    if (error.response) {
      // WhatsApp API returned an error
      res.status(error.response.status || 500).json({
        success: false,
        error: error.response.data?.error?.message || 'WhatsApp API error',
        http_code: error.response.status,
        details: error.response.data
      });
    } else if (error.request) {
      // Request was made but no response received
      res.status(500).json({
        success: false,
        error: 'Network error',
        message: 'No response from WhatsApp API',
        details: error.message
      });
    } else {
      // Error in request setup
      res.status(500).json({
        success: false,
        error: 'Internal server error',
        message: error.message
      });
    }
  }
});

// Legacy endpoint for backward compatibility (GoDaddy PHP endpoint name)
app.post('/whatsapp-proxy.php', async (req, res) => {
  // Redirect to main endpoint
  req.url = '/api/whatsapp-proxy';
  app._router.handle(req, res);
});

// Test endpoint for CORS
app.get('/test-cors', (req, res) => {
  res.json({
    status: 'success',
    message: 'CORS is working correctly',
    method: req.method,
    origin: req.headers.origin || 'Not set',
    headers: {
      'access-control-allow-origin': '*',
      'access-control-allow-methods': 'GET, POST, OPTIONS',
      'access-control-allow-headers': 'Content-Type, Authorization, X-Requested-With, Accept, Origin'
    },
    timestamp: new Date().toISOString()
  });
});

// Error handling middleware
app.use((err, req, res, next) => {
  console.error('Error:', err);
  res.status(500).json({
    success: false,
    error: 'Internal server error',
    message: err.message
  });
});

// 404 handler
app.use((req, res) => {
  res.status(404).json({
    success: false,
    error: 'Not found',
    message: `Route ${req.method} ${req.path} not found`
  });
});

// Start server
app.listen(PORT, () => {
  console.log(`🚀 WhatsApp API Proxy Server running on port ${PORT}`);
  console.log(`📍 Health check: http://localhost:${PORT}/health`);
  console.log(`📍 API endpoint: http://localhost:${PORT}/api/whatsapp-proxy`);
});

module.exports = app;

