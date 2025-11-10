# WhatsApp API Proxy - Backend

WhatsApp API Proxy server for Render.com deployment.

## 🚀 Deployment

### Render.com Deployment:

1. **Connect Repository**:
   - Go to [Render.com](https://render.com)
   - Connect your GitHub repository
   - Select this repository: `Api-wahstapp`

2. **Create Web Service**:
   - Service Type: `Web Service`
   - Environment: `Node`
   - Build Command: `npm install`
   - Start Command: `npm start`
   - Port: `10000` (or use `$PORT` environment variable)

3. **Deploy**:
   - Click "Create Web Service"
   - Wait for deployment to complete
   - Get your service URL: `https://your-service.onrender.com`

## 📁 Files

### Node.js Files:
- `server.js` - Express server (main file)
- `package.json` - Dependencies and scripts
- `render.yaml` - Render deployment configuration

### PHP Files (for GoDaddy):
- `.htaccess` - PHP handler and CORS headers
- `whatsapp-proxy.php` - PHP WhatsApp API proxy
- `test-*.php` - Test files

## 🔧 Configuration

### WhatsApp API:
- Endpoint: `https://waba.xtendonline.com/v3/{phone_number_id}/messages`
- Phone Number ID: `741032182432100`
- API Key: Configure in your application

## ✅ Features

- ✅ CORS support
- ✅ Error handling
- ✅ Input validation
- ✅ Location messages support
- ✅ Text messages support
- ✅ Clean JSON output
- ✅ Health check endpoint
- ✅ Render.com deployment ready

## 📝 Usage

### Text Message:
```bash
POST https://your-service.onrender.com/api/whatsapp-proxy
Content-Type: application/json

{
  "phone_number": "919090385555",
  "phone_number_id": "741032182432100",
  "api_key": "your-api-key",
  "message": "Hello World"
}
```

### Location Message:
```bash
POST https://your-service.onrender.com/api/whatsapp-proxy
Content-Type: application/json

{
  "phone_number": "919090385555",
  "phone_number_id": "741032182432100",
  "api_key": "your-api-key",
  "message_type": "location",
  "location": {
    "latitude": "18.5635",
    "longitude": "73.8024",
    "name": "Location Name",
    "address": "Location Address"
  }
}
```

## 🔍 Endpoints

### Health Check:
```
GET https://your-service.onrender.com/health
```

### Test CORS:
```
GET https://your-service.onrender.com/test-cors
```

### WhatsApp API Proxy:
```
POST https://your-service.onrender.com/api/whatsapp-proxy
```

## 🐛 Troubleshooting

### Render Deployment Issues:

1. **Package.json Missing**:
   - ✅ Fixed: `package.json` file included
   - Build command: `npm install`
   - Start command: `npm start`

2. **Port Configuration**:
   - ✅ Fixed: Uses `process.env.PORT` or default `3000`
   - Render automatically sets `PORT` environment variable

3. **Build Errors**:
   - Check Node.js version (18.0.0+)
   - Check npm version (9.0.0+)
   - Verify `package.json` is correct

### CORS Error:
- ✅ CORS middleware configured
- Test: `https://your-service.onrender.com/test-cors`

### API Errors:
- Check error logs in Render dashboard
- Verify WhatsApp API credentials
- Check network connectivity

## 📞 Support

For issues, check the logs in Render dashboard.

## 🔄 Local Development

```bash
# Install dependencies
npm install

# Start server
npm start

# Server will run on http://localhost:3000
```

## 🌐 Deployment Options

### Render.com (Recommended):
- ✅ Node.js support
- ✅ Free tier available
- ✅ Automatic deployments
- ✅ HTTPS enabled

### GoDaddy (PHP):
- Use PHP files (`.htaccess`, `whatsapp-proxy.php`)
- Upload to `public_html/api/` folder
- Set permissions: `644`
