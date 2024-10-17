#!/bin/bash
# Purpose: Create a new feature branch and push it to the remote repository
# Usage: ./create-feature-branch.sh <feature_branch_name>
# Description: This script creates a new feature branch locally, pushes it to the remote, and sets the upstream.

# Function to print messages with a timestamp
print_message() {
    echo ""
    echo "$(date +"%Y-%m-%d %H:%M:%S") - $1"
}

# Function to display help message
show_help() {
    echo "Usage: $0 <feature_branch_name>"
    echo ""
    echo "Options:"
    echo "  -h, --help     Display this help message."
    echo ""
    echo "Description:"
    echo "This script creates a new feature branch locally and pushes it to the remote repository."
    exit 0
}

# Function to handle errors
handle_error() {
    print_message "Error: $1"
    exit 1
}

# Parse command-line arguments
while [[ "$#" -gt 0 ]]; do
    case $1 in
        -h|--help) show_help ;;
        *) FEATURE_BRANCH_NAME=$1; shift ;;
    esac
done

# Ensure a branch name is provided
if [[ -z "$FEATURE_BRANCH_NAME" ]]; then
    handle_error "No feature branch name provided. Usage: $0 <feature_branch_name>"
fi

# Verify that git is installed
if ! command -v git &> /dev/null; then
    handle_error "Git is not installed. Please install Git to use this script."
fi

# Ensure the working directory is a valid git repository
if ! git rev-parse --is-inside-work-tree &> /dev/null; then
    handle_error "This is not a valid git repository. Please run this script inside a git repository."
fi

# Check if the branch already exists locally or remotely
if git show-ref --verify --quiet refs/heads/"$FEATURE_BRANCH_NAME"; then
    handle_error "Branch '$FEATURE_BRANCH_NAME' already exists locally."
fi

if git ls-remote --heads origin "$FEATURE_BRANCH_NAME" &> /dev/null; then
    handle_error "Branch '$FEATURE_BRANCH_NAME' already exists on the remote."
fi

# Create the new feature branch
print_message "Creating new feature branch: $FEATURE_BRANCH_NAME"
git checkout -b "$FEATURE_BRANCH_NAME" || handle_error "Failed to create feature branch: $FEATURE_BRANCH_NAME"

# Push the new branch to the remote and set upstream tracking
print_message "Pushing feature branch '$FEATURE_BRANCH_NAME' to the remote repository"
git push -u origin "$FEATURE_BRANCH_NAME" || handle_error "Failed to push feature branch to the remote."

# Show the current branch and status
print_message "Branch '$FEATURE_BRANCH_NAME' created and pushed successfully."
print_message "Current branch: $(git branch --show-current)"
git status || handle_error "Failed to show git status."

print_message "Script completed successfully."
