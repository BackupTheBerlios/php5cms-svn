#!/bin/sh

# Default non-web server user who will own the tree.
OWNER=root


chmod 777 tmp
cd tmp
chmod 777 cache
cd ..

chmod 777 var
cd var
chmod 777 nano
cd ..

echo "done."
