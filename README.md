# E-Commerce

## Setup

Dans le projet, 3 fichiers ne sont pas push. Ils sont présents dans l'archive envoyée.

### Env

2 fichiers env : 
* .env (à la racine du projet) -> /projectPath/app/.env  

exemple:
APP_ENV=dev

APP_SECRET=xxxxxxxxxx

JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem

JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem

JWT_PASSPHRASE=xxxxxxxxxxxxx

CORS_ALLOW_ORIGIN='^https?://(localhost|127\.0\.0\.1)(:[0-9]+)?$'

DATABASE_URL="mysql://xxxxx:xxxx@127.0.0.1:3306/ecommerce" -> user que l'on retrouve dans le vault

*  .env_client (à la racine du projet) -> /projectPath/client/.env  

exemple:
NUXT_PUBLIC_API_BASE_URL=https://localhost:8000/api
                 
### Clé SSH

Une clé ssh appelée EcommercekeyVPS qui est utilisée par ansible pour se connecter au VPS et ainsi installer le projet

-----BEGIN OPENSSH PRIVATE KEY-----
...
-----END OPENSSH PRIVATE KEY-----


### Vault

Un fichier vault_pass.txt est utilisé par ansible pour avoir le mot de passe du vault qui contient des passwords utilisés par ansible au moment de l'installation

exemple: 
motdepasse

Il est aussi important de configurer le ansible.cfg

exemple:
[defaults]
remote_user = xxxx
host_key_checking = False
vault_password_file = ./vault_pass.txt
