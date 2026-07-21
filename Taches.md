# Répartition des tâches — Simulateur OpérateurYas

# V1

---

## Étape 0 — Miaro Manda

- Architecture, git
- Le modèle de données : `Client`, `Compte`, `Opération`, `TypeOpération`, `Barème`, `Préfixe`, ......
- Base de donne
- Les contrats d'interface entre les deux modules, notamment :
  - `calculerFrais(typeOperation, montant) → montantFrais`

---

## Miaro — Côté Opérateur

- Configuration des préfixes valables (ex: 033, 037)
- Création des types d'opérations (dépôt, retrait, transfert)
- Gestion des barèmes de frais par tranche de montant (CRUD, modifiable)
- Moteur de calcul des frais (implémentation de `calculerFrais`)
- Situation des gains via les frais (retrait / transfert)
- Situation des comptes clients (vue d'ensemble, soldes)

---

## Manda — Côté Client
- Recherche template
- Login automatique par numéro de téléphone (vérification du préfixe, pas d'inscription préalable)
- Consultation du solde
- Dépôt (automatique)
- Retrait (automatique)
- Transfert entre comptes
- Historique des opérations

---

# V2

## Miaro — Côté Opérateur
- Configuration des préfixes des autres opérateurs (031, 032, ...)
- Configuration du pourcentage de commission pour les transferts inter-opérateurs
- Situation des gains séparée :
  - gains opérateur interne
  - gains provenant des autres opérateurs
- Situation des montants à reverser à chaque opérateur partenaire

---

## Manda — Côté Client
- Option « Inclure les frais de retrait dans le montant envoyé »
- Transfert vers un autre opérateur(il n’y a pas de frais de retrait pour les autres opérateurs)
- Envoi multiple vers plusieurs numéros avec répartition automatique du montant(même opérateur uniquement)
- Consultation du détail des frais avant validation d'un transfert

git tag -a v1.0 -m "Version 1.0"
git push origin v1.0