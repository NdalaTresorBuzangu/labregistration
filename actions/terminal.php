# Go to your project folder
cd "C:\xampp\htdocs\register_sample"

# Initialize git (only if not already initialized)
git init

# If the remote origin already exists, set it again (this replaces the old one)
git remote remove origin
git remote add origin https://github.com/NdalaTresorBuzangu/labregistration.git

# Add all project files
git add .

# Commit the changes with a message
git commit -m "Initial commit of register_sample project"

# Rename the main branch (if needed)
git branch -M main

# Pull existing changes from GitHub and rebase your local commits
git pull origin main --rebase

# Push your local project to GitHub after syncing
git push origin main


