
# **Security Policy for Mutillidae**

Mutillidae is a **deliberately vulnerable cybersecurity training platform** designed to help users learn about and test web application security concepts. It contains multiple levels of security configurations to facilitate learning by intentionally exposing vulnerabilities in lower levels.

---

## **Security Levels Overview**

- **Levels 1 to 4:**  
  These levels contain **intentional vulnerabilities** that are included as part of the training exercises.  
  - **Note:** Finding security issues in these levels is **normal and expected**.

- **Level 5:**  
  This is the **secure mode**. In Level 5, the application is configured to run with secure coding practices in place.  
  - **Expectation:** There should be **no known security issues** when the platform is set to **Level 5**.  
  - If any security issue is found in Level 5, please report it following the instructions below.

---

## **How to Report a Security Issue in Level 5**

If you discover a security vulnerability in **Level 5**:
1. **Open an issue** in the [Mutillidae GitHub repository](https://github.com/webpwnized/mutillidae/issues).
2. Provide:
   - A detailed description of the vulnerability.
   - Steps to reproduce the issue.
   - Any potential impact the vulnerability might have.

The maintainers will review and respond promptly to address the reported issue.

---

## **Disclosure Policy**

- Issues found in **Levels 1 to 4** are **not considered vulnerabilities** as they are intentionally included for educational purposes.
- Only **Level 5 security issues** are treated as actual vulnerabilities.
- We encourage **responsible disclosure** and appreciate contributions from the security community to help us improve the platform.

---

Thank you for helping us maintain the security of Mutillidae.
