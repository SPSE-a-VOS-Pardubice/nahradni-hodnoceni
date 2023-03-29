
# clone folder into build
rm -rf build
mkdir build
cp -r * build/
rm -rf build/vendor build/build

# run PHP 7.3 transpiling
cd build
composer install
vendor/bin/rector process src
