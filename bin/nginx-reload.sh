#!/bin/bash

echo "--- Restart nginx ------------------"
service nginx restart && service nginx status
echo "--- done. --------------------------"
echo ""
