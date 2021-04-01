# Projet_Web2

## Schémas de la base de donnée utilisée

### 1. Entité association

![schéma entité association](imagesCR/EntityRelationship.png)

### 2. Relationnel

![schéma relationnel](imagesCR/CMD.png)

Symfony ajoutant une clé primaire automatiquement, 
nous avons dû ajouter une contrainte unique 
sur les champs `client` et `produit` afin d'en faire notre clé primaire.