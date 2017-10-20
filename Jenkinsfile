#!groovy

def launchUnitTests = "yes"
def launchIntegrationTests = "yes"

class Globals {
    static pimVersion = "2.0"
    static extensionBranch = "2.0.x-dev"
}

stage("Checkout") {
    milestone 1
    if (env.BRANCH_NAME =~ /^PR-/) {
        userInput = input(message: 'Launch tests?', parameters: [
            choice(choices: 'yes\nno', description: 'Run unit tests', name: 'launchUnitTests'),
            choice(choices: 'yes\nno', description: 'Run integration tests', name: 'launchIntegrationTests'),
        ])

        launchUnitTests = userInput['launchUnitTests']
        launchIntegrationTests = userInput['launchIntegrationTests']
    }
    milestone 2

    node {
        deleteDir()
        checkout scm
        stash "extended_attributes"

        checkout([$class: 'GitSCM',
             branches: [[name: "${Globals.pimVersion}"]],
             userRemoteConfigs: [[credentialsId: 'github-credentials', url: 'https://github.com/akeneo/pim-community-standard.git']]
        ])
        stash "pim_community"

       checkout([$class: 'GitSCM',
         branches: [[name: "${Globals.pimVersion}"]],
         userRemoteConfigs: [[credentialsId: 'github-credentials', url: 'https://github.com/akeneo/pim-enterprise-standard.git']]
       ])
       stash "pim_enterprise"
   }
}

if (launchUnitTests.equals("yes")) {
    stage("Unit tests") {
        def tasks = [:]

        tasks["phpspec-7.1"] = {runPhpSpecTest("7.1")}
        tasks["php-cs-fixer-7.1"] = {runPhpCsFixerTest("7.1")}

        parallel tasks
    }
}

if (launchIntegrationTests.equals("yes")) {
    stage("Integration tests") {
        def tasks = [:]

        tasks["phpunit-7.1-ce"] = {runIntegrationTestCe("7.1")}
        tasks["phpunit-7.1-ee"] = {runIntegrationTestEe("7.1")}

        parallel tasks
    }
}

def runPhpSpecTest(version) {
    node('docker') {
        deleteDir()
        try {
            docker.image("akeneo/php:${version}")
            .inside("-v /home/akeneo/.composer:/home/akeneo/.composer -e COMPOSER_HOME=/home/akeneo/.composer") {
                unstash "extended_attributes"

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
            docker.image("akeneo/php:${version}")
            .inside("-v /home/akeneo/.composer:/home/akeneo/.composer -e COMPOSER_HOME=/home/akeneo/.composer") {
                unstash "extended_attributes"

                sh "composer install --optimize-autoloader --no-interaction --no-progress --prefer-dist"
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

def runIntegrationTestCe(version) {
    node('docker') {
        deleteDir()
        try {
            docker.image("elasticsearch:5.5")
            .withRun("--name elasticsearch -e ES_JAVA_OPTS=\"-Xms512m -Xmx512m\"") {
                docker.image("mysql:5.7")
                .withRun("--name mysql -e MYSQL_ROOT_PASSWORD=root -e MYSQL_USER=akeneo_pim -e MYSQL_PASSWORD=akeneo_pim -e MYSQL_DATABASE=akeneo_pim") {
                    docker.image("akeneo/php:${version}")
                    .inside("--link mysql:mysql --link elasticsearch:elasticsearch -v /home/akeneo/.composer:/home/akeneo/.composer -e COMPOSER_HOME=/home/akeneo/.composer") {
                        unstash "pim_community"

                        sh "composer require phpunit/phpunit akeneo/extended-attribute-type:${Globals.extensionBranch} --no-interaction --no-progress --prefer-dist"
                        dir("vendor/akeneo/extended-attribute-type") {
                            deleteDir()
                            unstash "extended_attributes"
                        }
                        sh 'composer dump-autoload -o'

                        sh "cp vendor/akeneo/extended-attribute-type/Tests/Resources/Jenkins/config/parameters_test.yml app/config/parameters_test.yml"

                        sh "sed -i 's#// your app bundles should be registered here#\\0\\nnew Pim\\\\Bundle\\\\ExtendedAttributeTypeBundle\\\\PimExtendedAttributeTypeBundle(),#' app/AppKernel.php"
                        sh "cat app/AppKernel.php"


                        sh "rm ./var/cache/* -rf"
                        sh "./bin/console --env=test pim:install --force"
                        sh "mkdir -p app/build/logs/"
                        sh "./vendor/bin/phpunit -c app/ --log-junit app/build/logs/phpunit.xml  vendor/akeneo/extended-attribute-type/Tests"
                    }
                }
            }
        } finally {
            sh "sed -i \"s/testcase name=\\\"/testcase name=\\\"[php-${version}] /\" app/build/logs/*.xml"
            junit "app/build/logs/*.xml"
            deleteDir()
        }
    }
}

def runIntegrationTestEe(version) {
    node('docker') {
        deleteDir()
        try {
            docker.image("elasticsearch:5.5")
            .withRun("--name elasticsearch -e ES_JAVA_OPTS=\"-Xms512m -Xmx512m\"") {
                docker.image("mysql:5.7")
                .withRun("--name mysql -e MYSQL_ROOT_PASSWORD=root -e MYSQL_USER=akeneo_pim -e MYSQL_PASSWORD=akeneo_pim -e MYSQL_DATABASE=akeneo_pim") {
                    docker.image("akeneo/php:${version}")
                    .inside("--link mysql:mysql --link elasticsearch:elasticsearch -v /home/akeneo/.composer:/home/akeneo/.composer -e COMPOSER_HOME=/home/akeneo/.composer") {
                        unstash "pim_enterprise"

                        sh "composer require phpunit/phpunit akeneo/extended-attribute-type:${Globals.extensionBranch} --no-interaction --no-progress --prefer-dist"
                        dir("vendor/akeneo/extended-attribute-type") {
                            deleteDir()
                            unstash "extended_attributes"
                        }
                        sh 'composer dump-autoload -o'

                        sh "cp vendor/akeneo/extended-attribute-type/Tests/Resources/Jenkins/config/parameters_test_ee.yml app/config/parameters_test.yml"

                        sh "sed -i 's#// your app bundles should be registered here#\\0\\nnew Pim\\\\Bundle\\\\ExtendedAttributeTypeBundle\\\\PimExtendedAttributeTypeBundle(),#' app/AppKernel.php"

                        sh "rm ./var/cache/* -rf"
                        sh "./bin/console --env=test pim:install --force"
                        sh "mkdir -p app/build/logs/"
                        sh "./vendor/bin/phpunit -c app/ --log-junit app/build/logs/phpunit.xml  vendor/akeneo/extended-attribute-type/Tests"
                    }
                }
            }
        } finally {
            sh "sed -i \"s/testcase name=\\\"/testcase name=\\\"[php-${version}] /\" app/build/logs/*.xml"
            junit "app/build/logs/*.xml"
            deleteDir()
        }
    }
}
