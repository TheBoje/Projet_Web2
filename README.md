# Projet_Web2

## Schémas de la base de donnée utilisée

### 1. Entité association

![schéma entité association](imagesCR/EntityRelationship.png)

### 2. Relationnel

![schéma relationnel](imagesCR/CMD.png)

Symfony ajoutant une clé primaire automatiquement, 
nous avons dû ajouter une contrainte unique 
sur les champs `client` et `produit` afin d'en faire notre clé primaire.

## Création d'un service sur Symfony

Avant de procéder à la création d'un service sous Symfony, nous allons expliquer briévement ce
à quoi cela correspond.

Un service ([selon M. Achref El Mouelhi](http://www.lsis.org/elmouelhia/courses/php/sf/coursSymfonyServices.pdf)) 
est une classe PHP ne réalisant qu'une seule fonctionnalité (envoi de mail, manipulation dans la base de donnée, ...)
qui se veut accessible partout dans le code et injectable dans les classes qui en ont besoin. Il a un
identifiant qui est son nom de classe.

Pour notre projet nous avons décider de faire un service inversant une chaîne de caractères et de l'afficher sur la
page d'accueil.

Dans un premier temps créé notre service dans le dossier `src/Services` :
```php
namespace App\Services;

class InvertString
{
    // retourne la chaîne de caractère inversé
    public function getInvertString(string $str) : string
    {
        return strrev($str);
    }
}
```
Ensuite, pour pouvoir utiliser le service nous devons passer un objet du type en paramètre d'une action qui souhaite
l'utiliser :

```php
public function action(InvertString $invertString)
{
    ...
    
    $inverted = $invertString->getInvertString("string"); // retourne "gnirts"
    
    ...
}
```