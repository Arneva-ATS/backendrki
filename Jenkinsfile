pipeline{
    agent any
    triggers{
        githubPush()
    }

    stages{
        stage('Build'){
            steps{
                sh 'docker build -t arneva-ats/backend-rki .'
            }
        }
        stage('Deliver'){
            steps {
                sh 'docker container rm --force rkiapp-container'
                sh 'docker run --name rkiapp-container -p  6000:6000 arneva-ats/backend-rki &'
                }
        }
    }
}
