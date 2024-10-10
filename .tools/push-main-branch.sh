#!/bin/bash
# Purpose: Push feature branch, merge into development branch, push development branch, merge into main branch, push main branch
# Usage: ./push-main-branch.sh <feature_branch> <version> <annotation>
# Description: This script pushes the feature branch, merges it into the development branch, pushes the development branch, merges it into the main branch, and pushes the main branch.

# Function to print messages with a timestamp
print_message() {
    echo ""
    echo "$(date +"%Y-%m-%d %H:%M:%S") - $1"
}

# Function to display help message
show_help() {
    echo "Usage: $0 <feature_branch> <version> <annotation>"
    echo ""
    echo "Options:"
    echo "  -h, --help     Display this help message."
    echo ""
    echo "Description:"
    echo "This script pushes the feature branch, merges it into the development branch,"
    echo "pushes the development branch, merges it into the main branch, and pushes the main branch."
    exit 0
}

# Function to handle errors
handle_error() {
    print_message "Error: $1"
    exit 1
}

# Parse options
while [[ "$#" -gt 0 ]]; do
    case $1 in
        -h|--help) show_help ;;
        *) break ;;
    esac
    shift
done

# Check if exactly three arguments are passed
if (( $# != 3 )); then
    handle_error "Incorrect number of arguments. Usage: $0 <feature_branch> <version> <annotation>"
fi

# Assign arguments to variables
FEATURE_BRANCH=$1
VERSION=$2
ANNOTATION=$3

# Verify 'git.sh' script exists and is executable
GIT_SCRIPT="./git.sh"
if [[ ! -x "$GIT_SCRIPT" ]]; then
    handle_error "'git.sh' script not found or not executable"
fi

# Push feature branch
print_message "Checking out feature branch: $FEATURE_BRANCH"
git checkout "$FEATURE_BRANCH" || handle_error "Failed to checkout feature branch: $FEATURE_BRANCH"

print_message "Pushing feature branch: $FEATURE_BRANCH"
"$GIT_SCRIPT" "$VERSION" "$ANNOTATION" || handle_error "Failed to push feature branch using git.sh"

# Merge feature branch into development branch
print_message "Checking out development branch"
git checkout development || handle_error "Failed to checkout development branch"

print_message "Merging feature branch '$FEATURE_BRANCH' into 'development'"
git merge "$FEATURE_BRANCH" || handle_error "Failed to merge feature branch into development branch"

# Push development branch
print_message "Pushing development branch"
"$GIT_SCRIPT" "$VERSION" "$ANNOTATION" || handle_error "Failed to push development branch using git.sh"

# Merge development branch into main branch
print_message "Checking out main branch"
git checkout main || handle_error "Failed to checkout main branch"

print_message "Merging development branch into 'main'"
git merge development || handle_error "Failed to merge development branch into main branch"

# Push main branch
print_message "Pushing main branch"
"$GIT_SCRIPT" "$VERSION" "$ANNOTATION" || handle_error "Failed to push main branch using git.sh"

print_message "Checking out feature branch: $FEATURE_BRANCH"
git checkout "$FEATURE_BRANCH" || handle_error "Failed to checkout feature branch: $FEATURE_BRANCH"

# Show git status
print_message "Git status"
git status || handle_error "Failed to show git status"

print_message "Script completed successfully"