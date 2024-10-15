#!/bin/bash
# Purpose: Push feature branch only
# Usage: ./push-feature-branch.sh <feature_branch> <version> <annotation>
# Description: This script pushes the feature branch using 'git.sh'.

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
    echo "This script pushes the feature branch using 'git.sh'."
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

# Check if the feature branch exists
if ! git show-ref --verify --quiet refs/heads/"$FEATURE_BRANCH"; then
    handle_error "Feature branch '$FEATURE_BRANCH' does not exist. Create it using:
    
    git checkout -b $FEATURE_BRANCH
    git push -u origin $FEATURE_BRANCH"
fi

# Push feature branch
print_message "Checking out feature branch: $FEATURE_BRANCH"
git checkout "$FEATURE_BRANCH" || handle_error "Failed to checkout feature branch: $FEATURE_BRANCH"

print_message "Pushing feature branch: $FEATURE_BRANCH"
"$GIT_SCRIPT" "$VERSION" "$ANNOTATION" || handle_error "Failed to push feature branch using git.sh"

# Show git status
print_message "Git status"
git status || handle_error "Failed to show git status"

print_message "Script completed successfully"
exit 0