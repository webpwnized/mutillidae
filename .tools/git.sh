#!/bin/bash
# Purpose: Tag a Git commit with version and annotation, commit locally, and push to remote
# Usage: ./git.sh <version> <annotation>
# Description: This script tags a Git commit with the specified version and annotation,
# commits locally, and pushes both the tag and commit to the remote repository.

# Function to print messages with a timestamp
print_message() {
    echo ""
    echo "$(date +"%Y-%m-%d %H:%M:%S") - $1"
}

# Function to display help message
show_help() {
    echo "Usage: $0 <version> <annotation>"
    echo ""
    echo "Options:"
    echo "  -h, --help     Display this help message."
    echo ""
    echo "Description:"
    echo "This script tags a Git commit with the specified version and annotation,"
    echo "commits locally, and pushes both the tag and commit to the remote repository."
    exit 0
}

# Function to handle errors
handle_error() {
    print_message "Error: $1"
}

# Parse options
while [[ "$#" -gt 0 ]]; do
    case $1 in
        -h|--help) show_help ;;
        *) break ;;
    esac
    shift
done

# Check if exactly two arguments are passed
if (( $# != 2 )); then
    handle_error "Incorrect number of arguments. Usage: $0 <version> <annotation>"
fi

# Assign arguments to variables
VERSION=$1
ANNOTATION=$2

# Tagging, committing, and pushing operations
print_message "Creating tag $VERSION with annotation \"$ANNOTATION\""
git tag -a "$VERSION" -m "$ANNOTATION" || handle_error "Failed to create tag"

print_message "Committing version $VERSION to local branch"
git commit -a -m "$VERSION $ANNOTATION" || handle_error "Failed to commit changes"

print_message "Pushing tag $VERSION to upstream"
git push --tag || handle_error "Failed to push tag to upstream"

print_message "Pushing version $VERSION to upstream"
git push || handle_error "Failed to push changes to upstream"

print_message "Script completed successfully"
