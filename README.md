# popscores.fr
A partir de ce dépot, vous pouvez réinstaller 'popscores'.
Seule la structure de la base de données est fournie.
## Présentation des fonctionnalités
### Gestion de la compétition
1. A la suite de votre connexion, sélectionnez la compétition. 
2. Créer le parcours : nombre de trous, formule de jeu, par
3. Listez les équipes et les joueurs
4. Lancez la compétition
5. Saisissez les scores
6. Validez et partagez les résultats  
### Gestion de la joueurs/équipes/écuries
Dans la page de gestion des équipes, bouton 'Nouveau'. 
Tout y est.
### Création des championnats
Passez par des commandes SQL
## Configuration de l'environnement de développement
### Fichiers Dockerfile et docker-compose
Tellement plus simple pour créer un environnement valide 
Par contre, je suis en 8.1 en conf de dév et en 7.1 en production. 
D'où un bug à la connexion en dèv. Un fois signé, il faut saisir à nouveau l'url : site/v0 
## Difficultés du site actuel
Le site est écrit comme aux débuts de PHP.
Pas de framework donc difficilement lisible pour un débutant. 
Donc, il faut savoir lire un mélange de HTML, PHP, Javascript et CSS.
A condition que j'en ait le temps et l'envie ... sorry
## A faire
Reprendre dans un framework Laravel ou Symfony. 
Avant cela, il me faut comprendre comment écrire des requêtes multi-tables dans ces frameworks ... tellement plus facile sans.  