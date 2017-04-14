#!groovy

def phpVersion = "5.6"
def mysqlVersion = "5.5"
def launchUnitTests = "yes"
def launchIntegrationTests = "yes"

class Globals {
<<<<<<< HEAD
    static pimVersion = "1.6"
    static extensionBranch = "1.0"
=======
    static pimVersion = "1.7"
    static extensionBranch = "dev-master"
>>>>>>> 1.7 migration
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

        tasks["phpspec-5.6"] = {runPhpSpecTest("5.6")}
        tasks["php-cs-fixer-5.6"] = {runPhpCsFixerTest("5.6")}

        parallel tasks
    }
}

if (launchIntegrationTests.equals("yes")) {
    stage("Integration tests") {
        def tasks = [:]

        tasks["phpunit-5.6-ce"] = {runIntegrationTestCe("5.6")}
        tasks["phpunit-5.6-ee"] = {runIntegrationTestEe("5.6")}
        tasks["phpunit-7.1-ce"] = {runIntegrationTestCe("7.1")}
        tasks["phpunit-7.1-ee"] = {runIntegrationTestEe("7.1")}

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

def runIntegrationTestCe(version) {
    node('docker') {
        deleteDir()
        try {
            docker.image("mysql:5.5").withRun("--name mysql -e MYSQL_ROOT_PASSWORD=root -e MYSQL_USER=akeneo_pim -e MYSQL_PASSWORD=akeneo_pim -e MYSQL_DATABASE=akeneo_pim") {
                docker.image("carcel/php:${version}").inside("--link mysql:mysql -v /home/akeneo/.composer:/home/akeneo/.composer -e COMPOSER_HOME=/home/akeneo/.composer") {
                    unstash "pim_community"

                    if (version != "5.6") {
                        sh "composer require --no-update alcaeus/mongo-php-adapter"
                    }

                    sh "composer require --no-update phpunit/phpunit:5.4 akeneo/extended-attribute-type:${Globals.extensionBranch}"
                    sh "composer update --ignore-platform-reqs --optimize-autoloader --no-interaction --no-progress --prefer-dist"
                    dir("vendor/akeneo/extended-attribute-type") {
                        deleteDir()
                        unstash "extended_attributes"
                    }
                    sh 'ln -s $(pwd)/vendor/akeneo/extended-attribute-type/doc/example/Pim src/Pim'
                    sh 'composer dump-autoload -o'

                    sh "cp vendor/akeneo/extended-attribute-type/doc/example/Pim/Bundle/ExtendedCeBundle/Resources/config/config_test.yml app/config/config_test.yml"
                    sh "cp vendor/akeneo/extended-attribute-type/doc/example/Pim/Bundle/ExtendedCeBundle/Resources/config/parameters_test.yml app/config/parameters_test.yml"

                    sh "sed -i 's#// your app bundles should be registered here#\\0\\nnew Pim\\\\Bundle\\\\ExtendedCeBundle\\\\ExtendedCeBundle(),#' app/AppKernel.php"
                    sh "sed -i 's#// your app bundles should be registered here#\\0\\nnew Pim\\\\Bundle\\\\ExtendedAttributeTypeBundle\\\\PimExtendedAttributeTypeBundle(),#' app/AppKernel.php"
                    sh "cat app/AppKernel.php"


                    sh "rm ./app/cache/* -rf"
                    sh "./app/console --env=test pim:install --force"
                    sh "mkdir -p app/build/logs/"
                    sh "./bin/phpunit -c app/ --log-junit app/build/logs/phpunit.xml  vendor/akeneo/extended-attribute-type/Tests"
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
            docker.image("mysql:5.5").withRun("--name mysql -e MYSQL_ROOT_PASSWORD=root -e MYSQL_USER=akeneo_pim -e MYSQL_PASSWORD=akeneo_pim -e MYSQL_DATABASE=akeneo_pim") {
                docker.image("carcel/php:${version}").inside("--link mysql:mysql -v /home/akeneo/.composer:/home/akeneo/.composer -e COMPOSER_HOME=/home/akeneo/.composer") {
                    unstash "pim_enterprise"

                    if (version != "5.6") {
                        sh "composer require --no-update alcaeus/mongo-php-adapter"
                    }

                    sh "composer require --no-update phpunit/phpunit:5.4 akeneo/extended-attribute-type:${Globals.extensionBranch}"
                    sh "composer update --ignore-platform-reqs --optimize-autoloader --no-interaction --no-progress --prefer-dist"
                    dir("vendor/akeneo/extended-attribute-type") {
                        deleteDir()
                        unstash "extended_attributes"
                    }
                    sh 'ln -s $(pwd)/vendor/akeneo/extended-attribute-type/doc/example/Pim src/Pim'
                    sh 'composer dump-autoload -o'

                    sh "cp vendor/akeneo/extended-attribute-type/doc/example/Pim/Bundle/ExtendedEeBundle/Resources/config/config_test.yml app/config/config_test.yml"
                    sh "cp vendor/akeneo/extended-attribute-type/doc/example/Pim/Bundle/ExtendedEeBundle/Resources/config/parameters_test.yml app/config/parameters_test.yml"

                    sh "sed -i 's#// your app bundles should be registered here#\\0\\nnew Pim\\\\Bundle\\\\ExtendedEeBundle\\\\ExtendedEeBundle(),#' app/AppKernel.php"
                    sh "sed -i 's#// your app bundles should be registered here#\\0\\nnew Pim\\\\Bundle\\\\ExtendedAttributeTypeBundle\\\\PimExtendedAttributeTypeBundle(),#' app/AppKernel.php"
                    sh "cat app/AppKernel.php"


                    sh "rm ./app/cache/* -rf"
                    sh "./app/console --env=test pim:install --force"
                    sh "mkdir -p app/build/logs/"
                    sh "./bin/phpunit -c app/ --log-junit app/build/logs/phpunit.xml  vendor/akeneo/extended-attribute-type/Tests"
                }
            }
        } finally {
            sh "sed -i \"s/testcase name=\\\"/testcase name=\\\"[php-${version}] /\" app/build/logs/*.xml"
            junit "app/build/logs/*.xml"
            deleteDir()
        }
    }
}
