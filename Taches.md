# Répartition des tâches — Simulateur Opérateur Mobile Money

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