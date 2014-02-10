#!/bin/sh
set -ev

composer install

[ -d tmp ] && rm -fr tmp
git clone -b gh-pages git@github.com:${GITHUB_USER}/primaids tmp
./vendor/bin/phpdoc.php --directory src --target tmp/${VERSION}/docs --template responsive-twig --defaultpackagename Chadicus --title "Primaids - Primitive Aids for PHP"

cd tmp
git add .
git commit -m "Build phpdocs"
git push origin gh-pages:gh-pages

cd ..
rm -fr tmp
