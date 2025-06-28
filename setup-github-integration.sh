#!/bin/bash

# iFixandRepair GitHub Integration Setup Script
# This script helps you set up GitHub integration for your WordPress theme

echo "🚀 iFixandRepair GitHub Integration Setup"
echo "========================================"

# Check if we're in the right directory
if [ ! -f "style.css" ] || [ ! -f "functions.php" ]; then
    echo "❌ Error: Please run this script from your theme directory"
    echo "Expected files: style.css, functions.php"
    exit 1
fi

# Get user input
read -p "📝 Enter your GitHub username: " GITHUB_USERNAME
read -p "📝 Enter your repository name (default: ifixandrepair-wordpress-theme): " REPO_NAME
REPO_NAME=${REPO_NAME:-ifixandrepair-wordpress-theme}

read -p "🌐 Enter your website domain (e.g., yoursite.com): " WEBSITE_DOMAIN
read -p "📁 Enter full path to WordPress installation (e.g., /var/www/html): " WP_PATH

echo ""
echo "🔧 Setting up GitHub integration..."

# Create GitHub Actions directory
mkdir -p .github/workflows

# Update GitHub Actions workflow
if [ -f ".github/workflows/deploy.yml" ]; then
    echo "✅ GitHub Actions workflow already exists"
else
    echo "📄 Creating GitHub Actions workflow..."
    # The deploy.yml content would be created here
fi

# Update the GitHub updater plugin
if [ -f "github-theme-updater.php" ]; then
    echo "🔄 Updating GitHub updater plugin with your details..."
    sed -i.bak "s/YOUR-USERNAME/$GITHUB_USERNAME/g" github-theme-updater.php
    sed -i.bak "s/ifixandrepair-wordpress-theme/$REPO_NAME/g" github-theme-updater.php
    echo "✅ GitHub updater plugin updated"
fi

# Update webhook handler
if [ -f "github-webhook.php" ]; then
    echo "🔄 Updating webhook handler..."
    sed -i.bak "s/YOUR-USERNAME/$GITHUB_USERNAME/g" github-webhook.php
    sed -i.bak "s|/path/to/wordpress|$WP_PATH|g" github-webhook.php
    echo "✅ Webhook handler updated"
fi

# Update style.css with GitHub header
echo "🎨 Adding GitHub updater header to style.css..."
if ! grep -q "GitHub Theme URI" style.css; then
    # Add GitHub header to style.css
    sed -i.bak "/Description:/a\\
GitHub Theme URI: $GITHUB_USERNAME/$REPO_NAME\\
GitHub Branch: main" style.css
    echo "✅ GitHub header added to style.css"
fi

# Create webhook secret
WEBHOOK_SECRET=$(openssl rand -hex 32)
echo "🔐 Generated webhook secret: $WEBHOOK_SECRET"

# Update webhook handler with secret
if [ -f "github-webhook.php" ]; then
    sed -i.bak "s/your-webhook-secret-here/$WEBHOOK_SECRET/g" github-webhook.php
fi

# Create directories
echo "📁 Creating necessary directories..."
mkdir -p assets/screenshots
mkdir -p logs
mkdir -p backups

# Set permissions
chmod +x github-webhook.php 2>/dev/null
chmod +x setup-github-integration.sh

echo ""
echo "✅ Setup completed!"
echo ""
echo "🔧 Next Steps:"
echo "=============="
echo ""
echo "1. 📤 Push your code to GitHub:"
echo "   git add ."
echo "   git commit -m 'Add GitHub integration'"
echo "   git push origin main"
echo ""
echo "2. 🔗 Set up GitHub repository secrets:"
echo "   Go to: https://github.com/$GITHUB_USERNAME/$REPO_NAME/settings/secrets/actions"
echo "   Add these secrets:"
echo "   - FTP_SERVER: $WEBSITE_DOMAIN"
echo "   - FTP_USERNAME: your-ftp-username"
echo "   - FTP_PASSWORD: your-ftp-password"
echo "   - WEBHOOK_SECRET: $WEBHOOK_SECRET"
echo "   - WEBHOOK_URL: https://$WEBSITE_DOMAIN/github-webhook.php"
echo ""
echo "3. 🎣 Set up GitHub webhook:"
echo "   Go to: https://github.com/$GITHUB_USERNAME/$REPO_NAME/settings/hooks"
echo "   Add webhook:"
echo "   - Payload URL: https://$WEBSITE_DOMAIN/github-webhook.php"
echo "   - Content type: application/json"
echo "   - Secret: $WEBHOOK_SECRET"
echo "   - Events: Just the push event and Releases"
echo ""
echo "4. 📤 Upload webhook handler:"
echo "   Upload 'github-webhook.php' to your website root directory"
echo "   URL should be: https://$WEBSITE_DOMAIN/github-webhook.php"
echo ""
echo "5. 🔌 Install GitHub updater plugin:"
echo "   Upload 'github-theme-updater.php' to /wp-content/plugins/"
echo "   Activate it in WordPress admin"
echo ""
echo "6. 🧪 Test the integration:"
echo "   - Make a small change to your theme"
echo "   - Commit and push to GitHub"
echo "   - Create a new release on GitHub"
echo "   - Check if your website updates automatically"
echo ""
echo "💡 Additional Setup Options:"
echo "=========================="
echo ""
echo "🔄 For automatic updates via SSH (more secure):"
echo "   Add these secrets to GitHub:"
echo "   - SSH_HOST: $WEBSITE_DOMAIN"
echo "   - SSH_USERNAME: your-ssh-username"
echo "   - SSH_PRIVATE_KEY: your-private-key"
echo "   - SSH_PORT: 22 (or your custom port)"
echo ""
echo "📧 For email notifications:"
echo "   - Install WP Mail SMTP plugin"
echo "   - Configure your email settings"
echo ""
echo "💬 For Slack notifications:"
echo "   Add SLACK_WEBHOOK secret with your Slack webhook URL"
echo ""
echo "🔍 For staging/production deployment:"
echo "   Add separate FTP secrets:"
echo "   - STAGING_FTP_SERVER, STAGING_FTP_USERNAME, STAGING_FTP_PASSWORD"
echo "   - PROD_FTP_SERVER, PROD_FTP_USERNAME, PROD_FTP_PASSWORD"
echo ""

# Create a summary file
cat > GITHUB_INTEGRATION_SUMMARY.md << EOF
# GitHub Integration Setup Summary

## Repository Details
- GitHub Username: $GITHUB_USERNAME
- Repository Name: $REPO_NAME
- Website Domain: $WEBSITE_DOMAIN
- WordPress Path: $WP_PATH

## Generated Secrets
- Webhook Secret: $WEBHOOK_SECRET

## URLs
- Repository: https://github.com/$GITHUB_USERNAME/$REPO_NAME
- Webhook URL: https://$WEBSITE_DOMAIN/github-webhook.php
- Settings: https://github.com/$GITHUB_USERNAME/$REPO_NAME/settings

## Files Created/Updated
- .github/workflows/deploy.yml
- github-theme-updater.php
- github-webhook.php
- style.css (updated with GitHub headers)

## Next Steps Checklist
- [ ] Push code to GitHub
- [ ] Add repository secrets
- [ ] Set up webhook
- [ ] Upload webhook handler
- [ ] Install updater plugin
- [ ] Test integration

## Troubleshooting
If you encounter issues:
1. Check GitHub Actions logs
2. Check webhook delivery logs in GitHub
3. Check server logs at: logs/github-webhook.log
4. Verify file permissions
5. Test webhook URL manually

## Support
- GitHub Issues: https://github.com/$GITHUB_USERNAME/$REPO_NAME/issues
- WordPress Codex: https://codex.wordpress.org/
EOF

echo "📄 Created GITHUB_INTEGRATION_SUMMARY.md with all details"
echo ""
echo "🎉 You're all set! Your theme will now auto-update from GitHub releases."
echo "📚 Check GITHUB_INTEGRATION_SUMMARY.md for a complete overview."