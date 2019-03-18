## Radish WebP for Woocommerce
Replaces normal images by WebP format.

**The plugin does not generate WebP images. It assumes you have already generated these with a plugin like [Optimus](https://wordpress.org/plugins/optimus/).**

## Development
Make sure to follow the following steps when contributing:
1. Clone the repo `git clone git@github.com:radishconcepts/separate-webp.git`
2. Run `composer install`
3. Run `npm install`
4. Run `gulp init`

Step 4 will initiate some standards, like creating a symlink to the .hooks/pre-commit file so that a code sniffer is ran before committing.

## Dev Features
- Codesniffer / PHP Coding Standards
- Composer autoload
- Includes pre-commit hook that will be activated with `gulp init`
