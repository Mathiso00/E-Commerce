# ECOMMERCE WEBSITE ULTIMATE GAMING GEAR

ULTIMATE GAMING GEAR is an e-commerce website created using Symfony, Nuxt.js and vuetify technologies.

## SETUP

To install the application, run the following commands in the terminal:

In the app folder, run the command compose install

In the client folder, run the command npm install


RUN
To start the site, run the following commands in the terminal:

In the app folder, type symfony server:start

In your browser, go to http://localhost:8000/api

In the client folder, type npm start

In your browser, go to http://localhost:3000


DEPLOYEMENT

In the project, 3 files are not pushed. They are present in the sent archive. These files are necessary for the deployment of the application.

Then run this command: "ansible-playbook playbook.yml -i hosts"

### Env

The project have 2 .env files : 
* .env (at the root of the project) -> /projectPath/app/.env  

example:
APP_ENV=dev

APP_SECRET=xxxxxxxxxx

JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem

JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem

JWT_PASSPHRASE=xxxxxxxxxxxxx

CORS_ALLOW_ORIGIN='^https?://(localhost|127\.0\.0\.1)(:[0-9]+)?$'

DATABASE_URL="mysql://xxxxx:xxxx@127.0.0.1:3306/ecommerce" -> user that can be found in the vault

*  .env_client (at the root of the project) -> /projectPath/client/.env  

example:
NUXT_PUBLIC_API_BASE_URL=https://localhost:8000/api
                 
### SSH KEY

An ssh key called EcommercekeyVPS which is used by ansible to connect to the VPS and install the project.

-----BEGIN OPENSSH PRIVATE KEY-----
...
-----END OPENSSH PRIVATE KEY-----


### Vault

A vault_pass.txt file is used by ansible to have the password of the vault which contains passwords used by ansible at installation time.

example: 
password

It is also important to configure the ansible.cfg

example:
[defaults]
remote_user = xxxx
host_key_checking = False
vault_password_file = ./vault_pass.txt

You must also modify the host file to define the server(s) to configure.
