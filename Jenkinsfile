#!groovy

def phpVersion = "5.6"
def mysqlVersion = "5.5"
def pimVersion = "1.6"
def launchUnitTests = "yes"

stage("Checkout") {
    milestone 1
    if (env.BRANCH_NAME =~ /^PR-/) {
        userInput = input(message: 'Launch tests?', parameters: [
            choice(choices: '1.6\n1.7', description: 'PIM version to use', name: 'pimVersion'),
            choice(choices: 'yes\nno', description: 'Run unit tests', name: 'launchUnitTests'),
        ])

        pimVersion = userInput['pimVersion']
        launchUnitTests = userInput['launchUnitTests']
    }
    milestone 2

    node {
        deleteDir()
        checkout scm
        stash "extended_attributes"
    }
}

if (launchUnitTests.equals("yes")) {
    stage("Unit tests") {
        def tasks = [:]

        tasks["phpspec-5.6"] = {runPhpSpecTest("5.6")}
        tasks["phpspec-7.0"] = {runPhpSpecTest("7.0")}
        tasks["phpspec-7.1"] = {runPhpSpecTest("7.1")}

        tasks["php-cs-fixer-5.6"] = {runPhpCsFixerTest("5.6")}
        tasks["php-cs-fixer-7.0"] = {runPhpCsFixerTest("7.0")}
        tasks["php-cs-fixer-7.1"] = {runPhpCsFixerTest("7.1")}

        parallel tasks
    }
}

def runPhpSpecTest(version) {
    node('docker') {
        deleteDir()
        try {
            docker.image("carcel/php:${version}").inside("-v /home/akeneo/.composer:/home/akeneo/.composer -e COMPOSER_HOME=/home/akeneo/.composer") {
                unstash "extended_attributes"

                if (version != "5.6") {
                    sh "composer require --no-update alcaeus/mongo-php-adapter"
                }
                
                sh "composer install --optimize-autoloader --no-interaction --no-progress --prefer-dist"
                sh "mkdir -p aklogs/"
                sh "./bin/phpspec run --no-interaction --format=junit > aklogs/phpspec.xml"
            }
        } finally {
            sh "sed -i \"s/testcase name=\\\"/testcase name=\\\"[php-${version}] /\" aklogs/*.xml"
            junit "aklogs/*.xml"
            deleteDir()
        }
    }
}

def runPhpCsFixerTest(version) {
    node('docker') {
        deleteDir()
        try {
            docker.image("carcel/php:${version}").inside("-v /home/akeneo/.composer:/home/akeneo/.composer -e COMPOSER_HOME=/home/akeneo/.composer") {
                unstash "extended_attributes"

                if (version != "5.6") {
                    sh "composer require --no-update alcaeus/mongo-php-adapter"
                }
                
                sh "composer install --ignore-platform-reqs --optimize-autoloader --no-interaction --no-progress --prefer-dist"
                sh "mkdir -p aklogs/"
                sh "./bin/php-cs-fixer fix --diff --format=junit --config=.php_cs.php > aklogs/phpcs.xml"
            }
        } finally {
            sh "sed -i \"s/testcase name=\\\"/testcase name=\\\"[php-${version}] /\" aklogs/*.xml"
            junit "aklogs/*.xml"
            deleteDir()
        }
    }
}
