   Install php dependencies
1. Run the command to install php dependencies
    php composer.phar install

2. Start the Google Cloud Datastore Emulator using command
    gcloud beta emulators datastore start

3. Start app using command.  Must be in root folder containing app.yaml
    dev_appserver.py app.yaml --php_executable_path /usr/local/php5/bin/php-cgi

4. To deploy to App engine run the command.  Must be in root folder containing app.yaml
    gcloud app deploy app.yaml -v 1