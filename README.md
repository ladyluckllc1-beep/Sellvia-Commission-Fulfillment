# Sellvia Commission Manager WordPress Plugin

This plugin integrates with your React-based Sellvia order management system.

## Installation Instructions

### 1. Upload Plugin to WordPress

1. Download the `sellvia-commission-manager` folder
2. Zip the folder (right-click > compress/zip)
3. Go to your WordPress admin: `https://tophitsemporium.shop/wp-admin`
4. Navigate to **Plugins > Add New > Upload Plugin**
5. Upload the zip file and click **Install Now**
6. Click **Activate Plugin**

### 2. Configure the Plugin

After activation, you need to update the React app URL:

1. Edit `sellvia-commission-manager.php`
2. Find line 54: `window.location.href = 'YOUR_REACT_APP_URL/orders';`
3. Replace `YOUR_REACT_APP_URL` with your actual React app URL (the Supabase hosting URL or your custom domain)

Example:
```javascript
window.location.href = 'https://your-project-name.supabase.co/orders';
```

### 3. Access the Dashboard

Once configured, you'll see **Sellvia Orders** in your WordPress admin menu. Clicking it will redirect you to your React-based order management interface.

## Features

- View all Sellvia orders
- Check pending commission balance
- Process orders using commission balance instead of credit card
- Real-time order status updates
- Secure API communication via Supabase Edge Functions

## Requirements

- WordPress 5.0 or higher
- WooCommerce (recommended for order management)
- PHP 7.4 or higher
- Active Sellvia account with API credentials

## Security

- All API credentials are stored securely in Supabase
- CORS protection enabled
- Webhook signature verification (implement as needed)

## Support

For issues or questions, contact your system administrator.

## Version

Current Version: 1.0.0
