# E-Commerce

## Setup

Dans le projet, 5 fichiers ne sont pas push. Ils sont présents dans l'archive envoyée.

### Env

2 fichiers env : 
* .env (à la racine du projet) -> /projectPath/app/.env  
*  .env_client (à la racine du projet) -> /projectPath/client/.env  
                 
### Clé SSH

Une clé ssh appelée EcommercekeyVPS qui est utilisée par ansible pour se connecter au VPS et ainsi installer le projet

### Vault


PARLER DES .ENV (.env.maria)
# To generate fake data
php bin/console doctrine:fixtures:load

Un fichier vault_pass.txt est utilisé par ansible pour avoir le mot de passe du vault qui contient des passwords utilisés par ansible au moment de l'installation

### JWT

Un dossier JWT est présent et contient deux clés. Celles-ci servent à l'encryptage et décryptage du token JWT. Ce dossier est deplacé par ansible dans le dossier /projectPath/app/config/jwt
Ces deux fichiers (private.pem et public.pem) peuvent être remplacé à condition de respecter le nommage
