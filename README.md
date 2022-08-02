# WP Query Builder
Relational Database Query builder for WordPress

<br>

## Dev Envirenment Setup

<ul>
    <li>Create a local WordPress envirenment setup.</li>
    <li>Create a basic plugin.</li>
    <li>Run <code>composer init</code> into the plugin.</li>
    <li>Clone <code>git@github.com:CodesVault/wp_querybuilder.git</code> into vendor folder.</li>
    <li>
        Add repository for local package in plugin's <code>composer.json</code>.
        <pre>
        "repositories": [
            {
                "type": "path",
                "url": "./vendor/wp_querybuilder"
            }
        ],
        </pre>
    </li>
    <li>Require this package. <code>composer require "codesvault/wpqb @dev"</code></li>
<ul>
