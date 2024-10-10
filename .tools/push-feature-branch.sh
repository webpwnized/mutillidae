#!/bin/bash
# Purpose: Push to a feature branch and optionally to the development branch.
# Usage: ./push-feature-branch.sh <version> <annotation> [-d|--push-to-development] [-b|--branch <branch-name>]
# Description: This script pushes a feature branch and optionally pushes to development.

# Function to print messages with a timestamp
print_message() {
    echo ""
    echo "$(date +"%Y-%m-%d %H:%M:%S") - $1"
}

# Function to display help message
show_help() {
    echo "Usage: $0 <version> <annotation> [-d|--push-to-development] [-b|--branch <branch-name>] [-h|--help]"
    echo ""
    echo "Options:"
    echo "  -h, --help                Display this help message."
    echo "  -d, --push-to-development Also push the feature branch to development."
    echo "  -b, --branch              Specify the feature branch to push."
    echo ""
    echo "Description:"
    echo "  This script pushes a specified feature branch and optionally merges it to the development branch."
    echo "  If the specified branch does not exist, you can create it with the following command:"
    echo "  git checkout -b <branch-name>"
    echo ""
    echo "Example:"
    echo "  ./push-feature-branch.sh 1.0.0 \"Initial commit\" -b feature/my-feature -d"
    exit 0
}

# Function to handle errors
handle_error() {
    print_message "Error: $1"
    exit 1
}

# Parse options
PUSH_DEV=false
FEATURE_BRANCH=""
while [[ "$#" -gt 0 ]]; do
    case $1 in
        -h|--help) show_help ;;
        -d|--push-to-development) PUSH_DEV=true ;;
        -b|--branch) shift; FEATURE_BRANCH=$1 ;;
        *) break ;;
    esac
    shift
done

# Check if version and annotation are passed
if (( $# != 2 )); then
    handle_error "Incorrect number of arguments. Version and annotation are required.
Version: A tag for the commit (e.g., '1.0.0').
Annotation: A description for the version (e.g., 'Initial release').
Usage: $0 <version> <annotation> [-d|--push-to-development] [-b|--branch <branch-name>] [-h|--help]"
    print_message "Also pushing feature branch $FEATURE_BRANCH to development branch"
    git checkout development || handle_error "Failed to checkout development branch"
    git merge "$FEATURE_BRANCH" --no-ff -m "Merging feature branch $FEATURE_BRANCH into development" || handle_error "Failed to merge feature branch into development"
    git push origin development || handle_error "Failed to push development branch"
fi

# Show git status
print_message "Git status"
git status || handle_error "Failed to show git status"

print_message "Script completed successfully"