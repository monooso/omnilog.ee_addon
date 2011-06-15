#!/bin/bash

#
# Script to initialise a new Git project. Performs the following steps:
# 1. Runs git init.
# 2. Adds all files to the repo.
# 3. Creates an "initial commit".
# 4. Creates a '0.1.0' tag.
# 5. Runs git flow init.
# 6. Prompts the user for a remote git repository address.
# 7. Sets the remote origin.
# 8. Pushes the master branch, and sets it to track origin/master.
# 9. Pushes the develop branch, and sets it to track origin/develop.
# 10. Pushes the 0.1.0 tag.
#

#
# Initialise the git repository.
#
echo "----------------------------------------"
echo "Initialising git repository..."
echo "----------------------------------------"
git init
git add .
git commit -m "Initial commit."

#
# Tag the release.
#
git tag "0.1.0" -m "Version 0.1.0"

#
# Initialise git flow. User is prompted for branch names.
#
echo "----------------------------------------"
echo "Initialising git flow..."
echo "----------------------------------------"
git flow init

#
# Prompt for remote origin.
#
echo "Enter remote repository address (or leave blank for none):"
read REMOTE_REPO

#
# Set the remote origin, and push.
#
if [ -n "$REMOTE_REPO" ]; then
    echo "----------------------------------------"
    echo "Pushing to remote repository..."
    echo "----------------------------------------"
    git remote add origin "$REMOTE_REPO"
    git checkout master
    git push -u origin master
    git push --tags
    git checkout develop
    git push -u origin develop
fi
