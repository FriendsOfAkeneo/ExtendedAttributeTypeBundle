#!groovy

def phpVersion = "5.6"
def mysqlVersion = "5.5"
def launchUnitTests = "yes"
def launchIntegrationTests = "yes"

class Globals {
    static pimVersion = "1.6"
    static extensionBranch = "dev-master"
}

stage("Checkout") {
    milestone 1
    if (env.BRANCH_NAME =~ /^PR-/) {
        userInput = input(message: 'Launch tests?', parameters: [
            choice(choices: '1.6\n1.7', description: 'PIM version to use', name: 'pimVersion'),
            choice(choices: 'yes\nno', description: 'Run unit tests', name: 'launchUnitTests'),
            choice(choices: 'yes\nno', description: 'Run integration tests', name: 'launchIntegrationTests'),
        ])

        Globals.pimVersion = userInput['pimVersion']
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
        tasks["phpspec-7.0"] = {runPhpSpecTest("7.0")}
        tasks["phpspec-7.1"] = {runPhpSpecTest("7.1")}

        tasks["php-cs-fixer-7.1"] = {runPhpCsFixerTest("7.0")}

        parallel tasks
    }
}

if (launchIntegrationTests.equals("yes")) {
    stage("Integration tests") {
        def tasks = [:]

        tasks["phpunit-5.6-ce"] = {runIntegrationTest("5.6")}

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

def runIntegrationTest(version) {
    node('docker') {
        deleteDir()
        docker.image("mysql:5.5").withRun("--name mysql -e MYSQL_ROOT_PASSWORD=root -e MYSQL_USER=akeneo_pim -e MYSQL_PASSWORD=akeneo_pim -e MYSQL_DATABASE=akeneo_pim") {
            docker.image("carcel/php:${version}").inside("--link mysql:mysql -v /home/akeneo/.composer:/home/akeneo/.composer -e COMPOSER_HOME=/home/akeneo/.composer") {
                unstash "pim_community"

                if (version != "5.6") {
                    sh "composer require --no-update alcaeus/mongo-php-adapter"
                }

                sh "composer require --no-update phpunit/phpunit akeneo/extended-attribute-type ${Globals.extensionBranch}"
                sh "composer update --ignore-platform-reqs --optimize-autoloader --no-interaction --no-progress --prefer-dist"

                dir("vendor/akeneo/extended_attributes") {
                    unstash "extended_attributes"
                }
                sh "composer dump-autoload -o"

                sh "cp app/config/parameters.yml.dist app/config/parameters_test.yml"
                sh "sed -i 's/database_host:     localhost/database_host:     mysql/' app/config/parameters_test.yml"
                sh "echo '' >> app/config/parameters_test.yml"
                sh "sed -i 's#// your app bundles should be registered here#\\0\\nnew Pim\\\\Bundle\\\\ExtendedAttributeTypeBundle\\\\PimExtendedAttributeTypeBundle(),#' app/AppKernel.php"
                sh "sed -i 's#// your app bundles should be registered here#\\0\\nnew Acme\\\\Bundle\\\\AppBundle\\\\AcmeAppBundle(),#' app/AppKernel.php"

                sh "cp vendor/akeneo/extended_attributes/doc/example/app/config/config_test.yml app/config/config_test.yml"

                 sh "./app/console --env=test pim:install --force"
            }
        }
    }
}
