# OWASP Mutillidae II

OWASP Mutillidae II is a free, open-source, deliberately vulnerable web application designed for web-security enthusiasts. It serves as a target for learning and practicing web security skills. Mutillidae can be easily installed on Linux and Windows systems using LAMP, WAMP, and XAMMP stacks. Additionally, it comes pre-installed on SamuraiWTF and OWASP BWA, and the existing version can be updated on these platforms. With dozens of vulnerabilities and hints to guide the user, Mutillidae provides an accessible web hacking environment suitable for labs, security enthusiasts, classrooms, CTFs, and vulnerability assessment tool targets. It has been widely used in graduate security courses, corporate web security training, and as an assessment target for vulnerability assessment software. OWASP Mutillidae II provides a comprehensive platform for learning and practicing web security techniques in a controlled environment.

## Project Announcements

Stay updated with project announcements on X: [webpwnized](https://x.com/webpwnized)

## Tutorials

Explore our tutorials on YouTube: [webpwnized YouTube channel](https://www.youtube.com/user/webpwnized)

## Installation Guides

### Location of source code

Note carefully that the source code has moved to the ***src*** project directory. **Be careful to adjust accordingly.**

### Standard Installation - DockerHub

- [How to Run Mutillidae from DockerHub Images](https://www.youtube.com/watch?v=c1nOSp3nagw)

### Alternative Installation - Docker

- [How to Install Docker on Ubuntu](https://www.youtube.com/watch?v=Y_2JVREtDFk)
- [How to Run Mutillidae on Docker](https://www.youtube.com/watch?v=9RH4l8ff-yg)

### Alternative Installation - Google Cloud

- [How to Run Mutillidae on Google Kubernetes Engine (GKE)](https://www.youtube.com/watch?v=uU1eEjrp93c)

### Legacy Installation - LAMP Stack

If you have a LAMP stack set up already, you can skip directly to installing Mutillidae. Check out our [comprehensive installation guide](README-INSTALLATION.md) for detailed instructions. Watch the video tutorial: [How to Install Mutillidae on LAMP Stack](https://www.youtube.com/watch?v=TcgeRab7ayM)

## Usage

Explore a large number of video tutorials available on the [webpwnized YouTube channel](https://www.youtube.com/playlist?list=PLZOToVAK85MrsyNmNp0yyUTBXqKRTh623) for guidance on using Mutillidae.

## Key Features

- Contains over 40 vulnerabilities and challenges, covering each of the OWASP Top Ten from 2007 to 2017
- Mutillidae is actually vulnerable, eliminating the need for users to enter a "magic" statement
- Easy installation on Linux or Windows *AMP stacks, including XAMPP, WAMP, and LAMP
- Preinstalled on Rapid7 Metasploitable 2, Samurai Web Testing Framework (WTF), and OWASP Broken Web Apps (BWA)
- One-click system restoration to default settings with the "Setup" button
- Users can switch between secure and insecure modes
- Widely used in graduate security courses, corporate web security training, and as an assessment target for vulnerability assessment software
- Regularly updated to maintain relevance and effectiveness

## Directory Structure

Below is the updated directory structure of the project along with brief descriptions:

### Root Directory
- `CHANGELOG.md` - Project change log.
- `CONTRIBUTING.md` - Contribution guidelines.
- `LICENSE` - Project license.
- `README-INSTALLATION.md` - Installation instructions.
- `README.md` - Main README file.
- `SECURITY.md` - Security guidelines.

### Source Directory: `src`
- **`ajax`** - Contains files related to AJAX functionality.
- **`classes`** - PHP class files for handling various tasks (e.g., logging, token management, database operations).
- **`data`** - Data files, such as XML data sources.
- **`documentation`** - Documentation files including installation guides and usage instructions.
- **`images`** - All image assets used in the application (e.g., icons, gritter assets).
- **`includes`** - Reusable PHP files (e.g., templates, configuration files).
- **`javascript`** - JavaScript libraries, custom scripts, and initializers for front-end functionality.
- **`labs`** - Files for security testing, offering challenges such as SQL injection, XSS, and file inclusions.
- **`passwords`** - Password-related files (e.g., account data).
- **`styles`** - CSS stylesheets defining the look and feel of the application.
- **`webservices`** - Web services for REST and SOAP APIs.
  - **`includes`** - Reusable components for web services.
  - **`rest`** - REST API files and related documentation.
  - **`soap`** - SOAP service files, including libraries and documentation.
    - **`soap/lib`** - Library files specifically for SOAP service integration.

### Additional Files and Directories
- `version` - Contains versioning information for the project.
