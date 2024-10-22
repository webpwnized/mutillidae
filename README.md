<a name="top"></a>
# $\color{LimeGreen}{OWASP\ Mutillidae\ II\ -\ Forked\ and\ enhanced\ to\ showcase\ DevSecOps\ pipelines\}$

[![JavaScript CodeQL Analysis](https://github.com/meleksabit/mutillidae/actions/workflows/scan-with-codeql.yml/badge.svg)](https://github.com/meleksabit/mutillidae/actions/workflows/scan-with-codeql.yml) [![Scan Application Code with Semgrep SAST](https://github.com/meleksabit/mutillidae/actions/workflows/scan-with-semgrep.yml/badge.svg?branch=development)](https://github.com/meleksabit/mutillidae/actions/workflows/scan-with-semgrep.yml) [![Scan with OWASP Dependency Check](https://github.com/meleksabit/mutillidae/actions/workflows/scan-with-owasp-dependency-check.yml/badge.svg)](https://github.com/meleksabit/mutillidae/actions/workflows/scan-with-owasp-dependency-check.yml) [![SonarCloud Analysis](https://github.com/meleksabit/mutillidae/actions/workflows/sonarcloud.yml/badge.svg)](https://github.com/meleksabit/mutillidae/actions/workflows/sonarcloud.yml) [![Scan PHP code with Snyk Code](https://github.com/meleksabit/mutillidae/actions/workflows/scan-with-snyk-code.yml/badge.svg)](https://github.com/meleksabit/mutillidae/actions/workflows/scan-with-snyk-code.yml) [![SonarQube Analysis](https://github.com/meleksabit/mutillidae/actions/workflows/sonarqube.yml/badge.svg)](https://github.com/meleksabit/mutillidae/actions/workflows/sonarqube.yml) [![GitHub Release](https://img.shields.io/github/v/release/meleksabit/mutillidae)](https://github.com/meleksabit/mutillidae/releases)

OWASP Mutillidae II is a free, open-source, deliberately vulnerable web application designed for web-security enthusiasts. It serves as a target for learning and practicing web security skills. Mutillidae can be easily installed on Linux and Windows systems using LAMP, WAMP, and XAMMP stacks. Additionally, it comes pre-installed on SamuraiWTF and OWASP BWA, and the existing version can be updated on these platforms. With dozens of vulnerabilities and hints to guide the user, Mutillidae provides an accessible web hacking environment suitable for labs, security enthusiasts, classrooms, CTFs, and vulnerability assessment tool targets. It has been widely used in graduate security courses, corporate web security training, and as an assessment target for vulnerability assessment software. OWASP Mutillidae II provides a comprehensive platform for learning and practicing web security techniques in a controlled environment.

## 📈🚀💡🏋️💪Enhancements and Additions in This Fork:
* **README Updates**: Enhanced the **`README.md`** file with improved documentation, ensuring better clarity and usability.
* **SonarQube Integration**:
    * Added **SonarQube analysis** to the project, enabling continuous code quality checks.
    * Included a **`Jenkinsfile`** for SonarQube integration to automate the process.
* **CodeQL Scan Improvements**: Enhanced the CodeQL scan process for better security analysis and coverage.
* **OWASP Dependency Check Workflow**: Optimized the **OWASP Dependency Check** workflow to improve the detection of vulnerable dependencies.
* **Semgrep SAST Workflow Updates**: Updated the **Semgrep SAST workflow** to use the latest versions for improved static analysis capabilities.
* **SonarCloud Workflow and Quality Gate**:
    * Introduced a **SonarCloud workflow** for seamless integration with SonarCloud’s continuous analysis tools.
    * Implemented a **Quality Gate** in the Jenkins pipeline to ensure code meets the required quality standards before deployment.

## $\color{red}{Project\ Announcements\}$

> [!TIP]
> Stay updated with project announcements on Twitter: [webpwnized](https://twitter.com/webpwnized)

## $\color{Melon}{Tutorials\}$

> [!TIP]
> Explore our tutorials on YouTube: [webpwnized YouTube channel](https://www.youtube.com/user/webpwnized)

## $\color{Aquamarine}{Installation\ Guides\}$

Please check the installation steps in [README-INSTALLATION.md](README-INSTALLATION.md)

## $\color{CarnationPink}{Location\ of\ source\ code\}$

> [!IMPORTANT]
> Note carefully that the source code ishas moved to the ***src*** project directory. **Be careful to adjust accordingly.**

## $\color{Goldenrod}{LAMP\ Stack\}$

> [!IMPORTANT]
> Note carefully that the source code ishas moved to the ***src*** project directory. **Be careful to adjust accordingly.** If you have a LAMP stack set up already, you can skip directly to installing Mutillidae. Check out our [comprehensive installation guide](README-INSTALLATION.md) for detailed instructions. Watch the video tutorial: [How to Install Mutillidae on LAMP Stack](https://www.youtube.com/watch?v=TcgeRab7ayM)

## $\color{ProcessBlue}{Docker\}$

> [!NOTE]
> Note carefully that the source code ishas moved to the ***src*** project directory. **Be careful to adjust accordingly.**

**Learn how to set up Mutillidae using Docker with our video tutorials:**

- [How to Install Docker on Ubuntu](https://www.youtube.com/watch?v=Y_2JVREtDFk)
- [How to Run Mutillidae on Docker](https://www.youtube.com/watch?v=9RH4l8ff-yg)
- [How to Run Mutillidae from DockerHub Images](https://www.youtube.com/watch?v=c1nOSp3nagw)
- [How to Run Mutillidae on Google Kubernetes Engine (GKE)](https://www.youtube.com/watch?v=uU1eEjrp93c)

## $\color{Magenta}{Usage\}$

> [!TIP]
> Explore a large number of video tutorials available on the [webpwnized YouTube channel](https://www.youtube.com/playlist?list=PLZOToVAK85MrsyNmNp0yyUTBXqKRTh623) for guidance on using Mutillidae.

## $\color{SeaGreen}{Key\ Features\}$

- Contains over 40 vulnerabilities and challenges, covering each of the OWASP Top Ten from 2007 to 2017
- Mutillidae is actually vulnerable, eliminating the need for users to enter a "magic" statement
- Easy installation on Linux or Windows *AMP stacks, including XAMPP, WAMP, and LAMP
- Preinstalled on Rapid7 Metasploitable 2, Samurai Web Testing Framework (WTF), and OWASP Broken Web Apps (BWA)
- One-click system restoration to default settings with the "Setup" button
- Users can switch between secure and insecure modes
- Widely used in graduate security courses, corporate web security training, and as an assessment target for vulnerability assessment software
- Regularly updated to maintain relevance and effectiveness

## $\color{purple}{Directory\ Descriptions\}$

```
.
└── src
    ├── ajax
    ├── classes
    ├── data
    ├── documentation
    ├── images
    │   └── gritter
    ├── includes
    │   └── hints
    ├── javascript
    │   ├── ddsmoothmenu
    │   ├── gritter
    │   ├── hints
    │   ├── inline-initializers
    │   ├── jQuery
    │   │   └── colorbox
    │   │       └── images
    │   │           └── ie6
    │   └── on-page-scripts
    ├── labs
    │   └── lab-files
    │       ├── click-jacking-lab-files
    │       ├── client-side-control-challenge
    │       ├── command-injection-lab-files
    │       ├── content-security-policy
    │       ├── cookie-lab-files
    │       ├── cross-site-request-forgery-lab-files
    │       ├── cross-site-scripting-lab-files
    │       ├── dependency-check-lab-files
    │       ├── file-identification-lab-files
    │       ├── hydra-lab-files
    │       ├── insecure-direct-object-references-lab-files
    │       ├── ldap-lab-files
    │       ├── local-file-inclusion-lab-files
    │       ├── mutillidae-project-options
    │       ├── netcat-lab-files
    │       ├── nikto-lab-files
    │       ├── open-redirects-lab-files
    │       ├── open-ssl-lab-files
    │       ├── password-cracking-lab-files
    │       ├── remote-file-inclusion-lab-files
    │       ├── scanning-scripts
    │       ├── sql-injection-lab-files
    │       ├── sqlmap-lab-files
    │       ├── sslscan-lab-files
    │       ├── tcpdump-lab-files
    │       ├── web-application-fuzzing-values
    │       │   ├── ascii
    │       │   ├── databases
    │       │   │   ├── mysql
    │       │   │   ├── oracle
    │       │   │   └── sql-server
    │       │   ├── ldap
    │       │   ├── operating-systems
    │       │   │   ├── linux
    │       │   │   └── windows
    │       │   └── overflow
    │       └── wireshark-lab-files
    ├── passwords
    ├── styles
    │   ├── ddsmoothmenu
    │   └── gritter
    └── webservices
        ├── rest
        └── soap
            └── lib

67 directories
```
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

[:arrow_up:](#top)