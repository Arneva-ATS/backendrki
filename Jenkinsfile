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
                sh 'docker container rm --force backend-rki'
                sh 'docker run --name backend-rki -p  6000:6000 arneva-ats/backend-rki &'
                }
        }
    }
    post {
        always {
            script {
                // Membersihkan image yang tidak digunakan
                sh 'docker image prune -f || true'
            }
        }
    }
}
