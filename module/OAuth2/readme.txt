curl -u testclient:testpass http://localhost/yougo/public/oauth2/oauth2/token -d grant_type=client_credentials

curl http://localhost/yougo/public/oauth2/oauth2/resource -d access_token=72fb47d9fea9c672d8fe909c22d315110e0ac97c

curl -u testclient:testpass http://localhost/example/oauth2/token.php -d grant_type=authorization_code&code=a596b2ed80834758fad45353411e63dd7394602d