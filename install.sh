echo "!!IMPORTANT!!"
echo "This will delete all previous setup of Explorer, and will start indexing from scratch"
echo "Are you sure you want to do this? (y/n)"
confirmation="n"
read confirmation
if [ $confirmation != "y" ]
    then
        exit
fi

rm .env
touch .env

########################
# Setup .env file      #
########################
echo APP_ENV=prod >> .env
echo APP_SECRET=app_secret >> .env
echo DATABASE_URL=postgres://postgres:@db:5432/blockchain_explorer >> .env

APP_TITLE="";
while [ -z $APP_TITLE ]; do
    echo "Enter app title"
    read APP_TITLE
done
echo APP_TITLE=$APP_TITLE >> .env

NODE_RPC_URL='';
while [ -z $NODE_RPC_URL ]; do
    echo "Enter your Node RPC Url"
    read NODE_RPC_URL
done
echo NODE_RPC_URL=$NODE_RPC_URL >> .env

APP_CONSENSUS_PROTOCOL='';
while [[ $APP_CONSENSUS_PROTOCOL != 'POA' ]] && [[ $APP_CONSENSUS_PROTOCOL != 'POW' ]]; do
    echo "Enter your consensus protocol. Allowed values are POA and POW"
    read APP_CONSENSUS_PROTOCOL
done
echo APP_CONSENSUS_PROTOCOL="\"$APP_CONSENSUS_PROTOCOL\"" >> .env

echo APP_STARTING_BLOCK=0 >> .env
echo APP_BLOCKS_PER_REQUEST=50 >> .env


########################
# Setup the project #
########################
# Copy docker-compose override
cp docker-compose.override.yml.dist docker-compose.override.yml
bash -c "docker kill $(docker ps -q)"
#bash -c "docker down $(docker ps -q)"
bash -c "docker-compose up -d --build --force-recreate"
bash -c "docker-compose exec php bash -c 'composer install --optimize-autoloader'"
bash -c "docker-compose exec php bash -c 'php bin/console doctrine:database:drop --force --if-exists'"
bash -c "docker-compose exec php bash -c 'php bin/console doctrine:database:create'"
bash -c "docker-compose exec php bash -c 'php bin/console doctrine:schema:update --force'"
bash -c "docker-compose exec php bash -c 'php bin/console blockchain:fetch:blocks'"
bash -c "docker-compose exec php bash -c 'php bin/console blockchain:fetch:transaction-receipts'"

########################
# Everything is done   #
########################
echo
echo "You have succesfully setup BlockchainExplorer, this is your config file"
echo
cat .env
echo
bash -c "docker-compose ps"
