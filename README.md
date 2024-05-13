# OWASP Mutillidae II

OWASP Mutillidae II is a free, open-source, deliberately vulnerable web application designed for web-security enthusiasts. It serves as a target for learning and practicing web security skills. Mutillidae can be easily installed on Linux and Windows systems using LAMP, WAMP, and XAMMP stacks. Additionally, it comes pre-installed on SamuraiWTF and OWASP BWA, and the existing version can be updated on these platforms. With dozens of vulnerabilities and hints to guide the user, Mutillidae provides an accessible web hacking environment suitable for labs, security enthusiasts, classrooms, CTFs, and vulnerability assessment tool targets. It has been widely used in graduate security courses, corporate web security training, and as an assessment target for vulnerability assessment software. OWASP Mutillidae II provides a comprehensive platform for learning and practicing web security techniques in a controlled environment.

## Project Announcements

Stay updated with project announcements on Twitter: [webpwnized](https://twitter.com/webpwnized)

## Tutorials

Explore our tutorials on YouTube: [webpwnized YouTube channel](https://www.youtube.com/user/webpwnized)

## Installation Guides

### Location of source code

Note carefully that the source code ishas moved to the ***src*** project directory. **Be careful to adjust accordingly.**

### LAMP Stack

Note carefully that the source code ishas moved to the ***src*** project directory. **Be careful to adjust accordingly.** If you have a LAMP stack set up already, you can skip directly to installing Mutillidae. Check out our [comprehensive installation guide](README-INSTALLATION.md) for detailed instructions. Watch the video tutorial: [How to Install Mutillidae on LAMP Stack](https://www.youtube.com/watch?v=TcgeRab7ayM)

### Docker

Note carefully that the source code ishas moved to the ***src*** project directory. **Be careful to adjust accordingly.**

Learn how to set up Mutillidae using Docker with our video tutorials:

- [How to Install Docker on Ubuntu](https://www.youtube.com/watch?v=Y_2JVREtDFk)
- [How to Run Mutillidae on Docker](https://www.youtube.com/watch?v=9RH4l8ff-yg)
- [How to Run Mutillidae from DockerHub Images](https://www.youtube.com/watch?v=c1nOSp3nagw)
- [How to Run Mutillidae on Google Kubernetes Engine (GKE)](https://www.youtube.com/watch?v=uU1eEjrp93c)

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

### Directory Descriptions

configurations.
- `src`: Main source directory.
    - `ajax`: Contains files related to AJAX functionality.
    - `classes`: Contains PHP class files.
    - `configuration`: Configuration files for Apache, hosts, HTTPS certificates, and OpenLDAP.
    - `documentation`: Documentation files.
    - `images`: Image files, including those used for gritter.
    - `includes`: Contains files with reusable code snippets or 
    - `javascript`: JavaScript files, including libraries and scripts.
    - `labs`: Lab files for security testing, including various challenges and vulnerabilities.
    - `passwords`: Password-related files.
    - `styles`: CSS stylesheets.
    - `webservices`: Web services files, including REST and SOAP services.
        - `soap/lib`: Library files for SOAP services.