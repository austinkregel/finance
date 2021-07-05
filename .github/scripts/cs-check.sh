#!/usr/bin/env bash
TARGET_BRANCH=main
if [ "$TARGET_BRANCH" = "" ]; then
  FILES="."
else
  FILES=$(git diff --diff-filter=d --name-only origin/${TARGET_BRANCH} --);
fi
for FILE in $FILES
do
   echo "Checking CS for: ${FILE}"
   vendor/bin/phpcs "$FILE" --standard=phpcs.xml -q #| vendor/bin/cs2pr
done
