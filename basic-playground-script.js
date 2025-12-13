/** WordPress Sandkasse (petj 2025) */ 

		// this function will convert blueprint JSON to a URL
        // and open the URL in the browser
        function aabn(landingPage) {

            // wp blueprint
            let bp = {
                "$schema": "https://playground.wordpress.net/blueprint-schema.json",
                "landingPage": landingPage,
                "login": true,
                "preferredVersions": {
                    "wp": "latest",
                    "php": "8.3"
                },
                "features": {
                    "networking": true
                },
                "steps": [
                    {
                        "step": "login",
                        "username": "admin",
                        "password": "password"
                    },
                    {
                        "step": "installPlugin",
                        "pluginData": {
                            "resource": "wordpress.org/plugins",
                            "slug": "custom-css-js"
                        },
                        "options": {
                            "activate": true
                        }
                    },
                    {
                        "step": "installPlugin",
                        "pluginData": {
                            "resource": "wordpress.org/plugins",
                            "slug": "wordpress-seo"
                        },
                        "options": {
                            "activate": false
                        }
                    },
                    {
                        "step": "installPlugin",
                        "pluginData": {
                            "resource": "wordpress.org/plugins",
                            "slug": "svg-support"
                        },
                        "options": {
                            "activate": true
                        }
                    },
                    {
                        "step": "installPlugin",
                        "pluginData": {
                            "resource": "wordpress.org/plugins",
                            "slug": "w3-total-cache"
                        },
                        "options": {
                            "activate": true
                        }
                    },
                    {
                        "step": "installPlugin",
                        "pluginData": {
                            "resource": "wordpress.org/plugins",
                            "slug": "font-awesome"
                        },
                        "options": {
                            "activate": true
                        }
                    },
                    {
                        "step": "setSiteOptions",
                        "options": {
                            "blogname": "WordPress Sandkassen"
                        }
                    }
                ]
            }

            // convert to a string
            let minUrl = JSON.stringify(bp)

            // open the location
            location.href = "https://playground.wordpress.net/#" + minUrl
        }

        // this will open Playground in Styles
        // aabn("/wp-admin/site-editor.php?p=%2Fstyles")

// you could also open the frontpage
// aabn("/")