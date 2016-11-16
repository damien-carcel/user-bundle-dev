#!groovy

stage 'Clone'
node {
    step([$class: 'GitHubSetCommitStatusBuilder'])
    deleteDir()
    checkout scm
}
