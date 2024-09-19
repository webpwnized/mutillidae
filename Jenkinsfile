pipeline {
    agent any
    
    tools {
        // Define tools if needed (optional for PHP)
    }

    environment {
        SONAR_TOKEN = credentials('SONAR_TOKEN')  // SonarQube token from Jenkins credentials
        MYSQL_ROOT_PASSWORD = 'root'              // MySQL root password for the MySQL container
    }

    stages {
        stage('Start MySQL Service') {
            steps {
                script {
                    // Start MySQL as a service container
                    sh '''
                    docker run --name mysql-server -e MYSQL_ROOT_PASSWORD=${MYSQL_ROOT_PASSWORD} -d mysql:5.7
                    '''
                }
            }
        }

        stage('SonarQube Analysis') {
            steps {
                withCredentials([string(credentialsId: 'SONAR_TOKEN', variable: 'SONAR_TOKEN')]) {
                    script {
                        // Run SonarScanner for PHP with the correct SonarQube server URL and project key
                        sh '''
                        sonar-scanner \
                          -Dsonar.projectKey=Mutillidae-II \
                          -Dsonar.sources=. \
                          -Dsonar.host.url=http://localhost:9000 \
                          -Dsonar.login=$SONAR_TOKEN \
                          -Dsonar.php.tests.reportPath=./test-reports/unit-report.xml \
                          -Dsonar.php.coverage.reportPaths=./test-reports/coverage.xml
                        '''
                    }
                }
            }
        }

        stage('Build PHP Application Docker Image') {
            steps {
                withDockerRegistry([credentialsId: "dockerlogin", url: ""]) {
                    script {
                        // Build the PHP application Docker image
                        app = docker.build("angel3/mutillidae:latest")
                    }
                }
            }
        }
    }

        stage('Quality Gate') {
            steps {
                script {
                    // Wait for the quality gate result from SonarQube
                    timeout(time: 10, unit: 'MINUTES') {
                        def qg = waitForQualityGate()
                        if (qg.status != 'OK') {
                            error "Pipeline failed due to SonarQube quality gate failure: ${qg.status}"
                        }
                    }
                }
            }
        }

    post {
        always {
            stage('Tear Down') {
                steps {
                    script {
                        // Stop MySQL container after use
                        sh 'docker stop mysql-server && docker rm mysql-server'
                    }
                }
            }
        }
    }
}
