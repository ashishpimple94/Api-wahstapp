# WhatsApp API Proxy - Backend

WhatsApp API Proxy server for GoDaddy deployment.

## 📁 Files

### Required Files:
- `.htaccess` - PHP handler and CORS headers
- `whatsapp-proxy.php` - Main WhatsApp API proxy

### Test Files:
- `test-cors.php` - CORS test
- `test-error.php` - PHP environment test
- `test-proxy.php` - Proxy test
- `test-api.php` - API test
- `test-whatsapp-simple.php` - Simple WhatsApp test
- `test-whatsapp.php` - WhatsApp test

## 🚀 Deployment

### GoDaddy Deployment:

1. Upload all files to `public_html/api/` folder
2. Set file permissions: `644`
3. Test: `https://yourdomain.com/api/test-error.php`

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

## 📝 Usage

### Text Message:
```json
{
  "phone_number": "919090385555",
  "phone_number_id": "741032182432100",
  "api_key": "your-api-key",
  "message": "Hello World"
}
```

### Location Message:
```json
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

## 🐛 Troubleshooting

### CORS Error:
- Check `.htaccess` file is uploaded
- Verify CORS headers in PHP file
- Test: `https://yourdomain.com/api/test-cors.php`

### PHP Not Working:
- Check PHP version (7.4+)
- Enable cURL extension
- Test: `https://yourdomain.com/api/test-error.php`

### File Permissions:
- Files: `644`
- Folders: `755`

## 📞 Support

For issues, check the error logs in GoDaddy cPanel.

