
# **Contributing to Mutillidae**

We welcome contributions to **Mutillidae**! To ensure a smooth collaboration, please follow the steps outlined below for submitting changes via **feature branches**.

---

## **Getting Started**
1. **Fork the Repository:**
   - Click the **Fork** button at the top-right corner of the [Mutillidae GitHub repository](https://github.com/webpwnized/mutillidae).

2. **Clone Your Fork:**
   ```bash
   git clone https://github.com/<your-username>/mutillidae.git
   cd mutillidae
   ```

3. **Set the Original Repository as Upstream:**
   - Add the upstream repository to stay in sync with the main project:
   ```bash
   git remote add upstream https://github.com/webpwnized/mutillidae.git
   ```

---

## **Creating a Feature Branch**
1. **Create a New Branch for Your Changes:**
   - Use a descriptive branch name that reflects the feature or issue being worked on:
   ```bash
   git checkout -b feature/your-feature-name
   ```

2. **Make Your Changes:**
   - Add, modify, or remove files as needed.

3. **Commit Your Changes:**
   - Write meaningful commit messages:
   ```bash
   git add .
   git commit -m "feat: Brief description of the changes"
   ```

---

## **Sync with the Upstream Repository**
Before submitting your changes, make sure your feature branch is up-to-date with the latest code from the main repository.

1. **Fetch and Merge Changes from Upstream:**
   ```bash
   git fetch upstream
   git merge upstream/main
   ```

2. **Resolve Any Merge Conflicts (if needed):**
   - If conflicts arise, carefully resolve them before proceeding.

---

## **Push and Submit a Pull Request**
1. **Push Your Feature Branch to Your Fork:**
   ```bash
   git push origin feature/your-feature-name
   ```

2. **Open a Pull Request:**
   - Go to the [Mutillidae repository](https://github.com/webpwnized/mutillidae) and click on **New Pull Request**.
   - Select your feature branch and submit the pull request with a clear title and description.

---

## **PR Review and Feedback**
- A project maintainer will review your pull request. Be prepared to make changes if feedback is provided.
- Once your pull request is approved, it will be merged into the main repository.

---

## **Guidelines for Contributors**
- Follow the **feature branch** GitOps model: each feature or bug fix should have its own branch.
- Use meaningful commit messages following the **conventional commits** style (`feat:`, `fix:`, `docs:`, etc.).
- Ensure your changes do not break existing functionality by running tests (if applicable).
- Keep your pull requests small and focused on a single issue or feature to make reviews easier.

---

Thank you for your contribution! Together, we can make **Mutillidae** even better.
