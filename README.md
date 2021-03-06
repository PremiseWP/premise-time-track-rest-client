# Premise Time Track REST Client

This is a client for the Wordpress REST API, using OAuth 1.0a to connect to the Premise Time Tracker plugin.

This is *also* the repository for a WordPress provider for the [OAuth 1.0a client library][league-oauth] by the League of Extraordinary Packages.

[league-oauth]: https://github.com/thephpleague/oauth1-client

## Setup (Server)

On your server, you need the latest [REST API plugin][] and [the OAuth server plugin][oauth] plugins installed.
Note: the Oauth server plugin **v.0.3.0** has an **error**.
To fix it, please replace line 736 in the `lib/class-wp-rest-oauth1.php` file:
```php
$param_key = $key . '[' . $param_key . ']'; // Handle multi-dimensional array
```

[REST API plugin]: https://wordpress.org/plugins/rest-api/
[oauth]: https://github.com/WP-API/OAuth1

You also need the [Premise Time Tracker](https://github.com/PremiseWP/premise-time-track/) plugin installed.

## Setup (Client)

To run the client here, you need to grab this repo, then install the dependencies via Composer. You can then run the example client via PHP's built-in server:

```
# Clone down this repo
git clone https://github.com/PremiseWP/premise-time-track-rest-client
cd premise-time-track-rest-client

# Install dependencies
composer install

# Replace the `vendor/league/oauth1-client/scr/Client/Signature/HmacSha1Signature.php`
# with the one you will find in the root folder.

# Edit the `config.inc.php` file (refer to the `config.inc.sample.php` file)

# Run the client on port 8080
php -S 0.0.0.0:8080
```

Then open up http://localhost:8080/ in your browser to get started.

## Usage

The client is split into four stages, mirroring how typical applications work (with the exception of the second step).

### Step 1: Discovery

The first step is finding the REST API. We start out here by asking the user (in this case, you) for the site address.

This step will be skipped if you set the address in the `config.inc.php` file.

<img src="http://i.imgur.com/m03qws1.png" />

We use this URL to find the API by checking for a `Link` header with the link relation (`rel`) set to `https://api.w.org/`. We use [the existing library][discovery-php] for this to simplify the code.

Once we have the API URL, we request it, which gives us back the index for the site's API. We then check the index to make sure OAuth 1.0a is supported (by checking `authentication.oauth1`), and get the links from there.

[discovery-php]: https://github.com/WP-API/discovery-php


### Step 2: Input Credentials

The next step is asking for your client credentials. Typically this won't be required, as your client credentials should be part of your application. (We're working on solutions to get this working across all WP sites on the internet with a single key/secret.)

<img src="http://i.imgur.com/COQZrDW.png" />

Plug these in and start the authorization process.

In the background, after clicking "Begin Authorization", we kick off the process by asking for temporary credentials (a request token). Once we have these, we then redirect to the authorization page on the site itself to authorize the credentials.


### Step 3: Authorization

You'll be redirected to the site itself to authorize the token. This is where we link the request token to the user's account, and authorize it for the final step.

<img src="http://i.imgur.com/E1uwSNw.png" />

Once the user clicks "authorize", the site will do a few things:

1. Links your request token to the user
2. Marks the token as authorized, allowing it to be upgraded to permanent credentials (access token)
3. Generates a verifier token to avoid CSRF
4. Finally, redirect back to your callback URL with the verifier token

The demo client will then complete the process by exchanging the temporary credentials (request token) for permanent credentials (access token). Congratulations, the client is now linked to the site!


### Step 4: Display Timers details

We display the Timers details, which should let you verify that we're getting the real details from the site.

### 400 No Oauth parameters supplied error

If you experience a "400 No Oauth parameters supplied error", this may be due to the Authorization header blocked.
Edit your **Wordpress** `.htaccess` file this way:

```
# REST API fix 400 error.
# RewriteRule ^index\.php$ - [L]
RewriteRule ^index\.php$ - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization},L]
```

### New client / project / timesheet not appearing in the list

When creating a new Timer and adding a new client / project / timesheet, it may not be displayed right away.
If you experience this issue, please check your Wordpress installation for _cache plugins_ and deactivate them.


### Security rule error

In case the Wordpress page you are trying to view is restricted due to a security rule.
Please disable any `mod_security` (Apache) rule blocking `iframe` on you Wordpress server.


## License

This project is licensed under the MIT license. See [LICENSE.md](LICENSE.md) for the full license.
