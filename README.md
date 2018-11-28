# Blockchain Explorer
Very naive implementation of Ethereum blockchain explorer. Created for specific purpose of being easy to install and able to work with POA blockchains. This is still in very early stages, and is currently only used by AsyncLabs for testing during development.

## Installing / Getting started

### Prerequisites

To start using BlockchainExplorer, you need to have `docker` & `docker-compose` installed on your host machine.

[How to install docker](https://docs.docker.com/compose/install/)


### Cloning Project

Create a folder in which you are going to store the Explorer, and change your path into it, after that simply clone the project from Github.

```shell
mkdir BlockchainExplorer
cd BlokchainExplorer
git clone git@github.com:asynclabsco/blockchain-explorer.git
```

### Installation process
#### Run the instalation with install.sh script
```shell
./install.sh
```
#### Sample input and output
```shell
!!IMPORTANT!!
This will delete all previous setup of Explorer, and will start indexing from scratch
Are you sure you want to do this? (y/n)
y
Enter app title
AsyncPrivate
Enter your Node RPC Url
http://<ip-of-rpc-node>:<port>
Enter your consensus protocol. Allowed values are POA and POW
POA
```
For RPC node enter RPC node of your network. Infura links work too.

## Developing
After you've setup project with install script, that's it. If you have any problems with running docker containers, you can always open an issue with a question.

## Features

What's all the bells and whistles this project can perform?
* Show mined blocks
* Show transactions from mined blocks
* Add addresses to database and see their ETH balance
* SmartContract viewer
* Recognize ERC20 and ERC721 tokens from Contracts


### Todo
- [ ] Search
- [ ] Improved ERC tokens recognition (and better tested)
- [ ] View balances of ERC20 tokens for user
- [ ] In general better display of ERC tokens
- [ ] Tests in general

## Contributing
Feel free to fork and submit pull requests back to this repository. :)

## Licensing
MIT license.


