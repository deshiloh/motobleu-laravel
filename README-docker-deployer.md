# Docker + Deployer (Local)

Ce fichier décrit comment mettre en place un environnement de test pour un déploiement Laravel avec [Deployer](https://deployer.org/) et Docker.

---

## 📁 Structure du projet

```
docker-deployer-test/
├── app/                  # Votre projet Laravel
├── docker-compose.yml
├── Dockerfile
└── ssh/
    └── id_rsa
    └── id_rsa.pub
```

---

## ⚙️ Étapes d'installation

### 1. Générer une paire de clés SSH

Dans le dossier `docker-deployer-test/ssh` :

```bash
ssh-keygen -t rsa -b 4096 -f ./ssh/id_rsa -N ""
```

> Cela générera `id_rsa` (clé privée) et `id_rsa.pub` (clé publique).  
> La clé publique est copiée automatiquement dans le conteneur pour permettre la connexion SSH.

---

### 2. Lancer les conteneurs

```bash
docker compose up -d --build
```

---

### 3. Vérifier la connexion SSH (optionnel)

```bash
ssh -p 2222 deploy@localhost -i ./ssh/id_rsa
```

Mot de passe : `deploy` (par défaut)

---

### 4. Déployer avec Deployer

Dans le dossier `app/` (où se trouve Laravel) :

```bash
sail php vendor/bin/dep deploy local-docker
```

---

## 🐘 Configuration MySQL

Voici les paramètres à utiliser dans `.env` de Laravel :

```
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=motobleu_laravel
DB_USERNAME=laravel
DB_PASSWORD=secret
```

---

## 🧼 Nettoyage

Pour tout arrêter :

```bash
docker compose down -v
```

---

## 📝 Notes

- Le conteneur expose le port SSH `2222`
- L'utilisateur `deploy` est créé automatiquement
- MySQL est configuré avec un volume persistant (`mysql_data`)
