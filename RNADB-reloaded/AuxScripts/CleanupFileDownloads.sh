#!/bin/sh

#find /httpsdocs/Downloads -name *.* -mmin +59 -delete > /dev/null
#/bin/bash -c "find /httpsdocs/Downloads -mmin +59 -delete"
/bin/bash -c "find /httpsdocs/Downloads/* -mmin +59 -delete"
