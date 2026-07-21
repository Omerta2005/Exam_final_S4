PRAGMA foreign_keys = ON;

CREATE TABLE Operateur (
    id_operateur    INTEGER PRIMARY KEY AUTOINCREMENT,
    nom             TEXT NOT NULL
);

CREATE TABLE Prefixe (
    id_prefixe      INTEGER PRIMARY KEY AUTOINCREMENT,
    id_operateur    INTEGER NOT NULL,
    code            TEXT NOT NULL UNIQUE,
    actif           INTEGER NOT NULL DEFAULT 1,
    FOREIGN KEY (id_operateur) REFERENCES Operateur(id_operateur)
);

CREATE TABLE Client (
    id_client         INTEGER PRIMARY KEY AUTOINCREMENT,
    numero_telephone  TEXT NOT NULL UNIQUE,
    nom               TEXT,
    id_operateur      INTEGER NOT NULL,
    FOREIGN KEY (id_operateur) REFERENCES Operateur(id_operateur)
);

CREATE TABLE Compte (
    id_compte       INTEGER PRIMARY KEY AUTOINCREMENT,
    id_client       INTEGER NOT NULL,
    solde           REAL NOT NULL DEFAULT 0,
    date_creation   TEXT NOT NULL DEFAULT (datetime('now')),
    FOREIGN KEY (id_client) REFERENCES Client(id_client)
);

CREATE TABLE TypeOperation (
    id_type_operation   INTEGER PRIMARY KEY AUTOINCREMENT,
    libelle             TEXT NOT NULL UNIQUE
);

CREATE TABLE BaremeFrais (
    id_bareme           INTEGER PRIMARY KEY AUTOINCREMENT,
    id_operateur        INTEGER NOT NULL,
    id_type_operation   INTEGER NOT NULL,
    montant_min         REAL NOT NULL,
    montant_max         REAL NOT NULL,
    valeur_frais        REAL NOT NULL,
    FOREIGN KEY (id_type_operation) REFERENCES TypeOperation(id_type_operation),
    FOREIGN KEY (id_operateur) REFERENCES Operateur(id_operateur),
    CHECK (montant_max > montant_min)
);

CREATE TABLE statut_operation (
    id_statut   INTEGER PRIMARY KEY AUTOINCREMENT,
    libelle     TEXT NOT NULL UNIQUE
);

CREATE TABLE Operation (
    id_operation          INTEGER PRIMARY KEY AUTOINCREMENT,
    id_type_operation     INTEGER NOT NULL,
    id_compte_source      INTEGER,
    id_compte_destination INTEGER,
    id_operateur_destination INTEGER,
    montant               REAL NOT NULL,
    frais_appliques       REAL NOT NULL DEFAULT 0,
    date_operation        TEXT NOT NULL DEFAULT (datetime('now')),
    id_statut              INTEGER NOT NULL DEFAULT 1,
    FOREIGN KEY (id_type_operation) REFERENCES TypeOperation(id_type_operation),
    FOREIGN KEY (id_compte_source) REFERENCES Compte(id_compte),
    FOREIGN KEY (id_compte_destination) REFERENCES Compte(id_compte),
    FOREIGN KEY (id_operateur_destination) REFERENCES Operateur(id_operateur),
    FOREIGN KEY (id_statut) REFERENCES statut_operation(id_statut)
);

CREATE INDEX idx_prefixe_operateur ON Prefixe(id_operateur);
CREATE INDEX idx_client_numero ON Client(numero_telephone);
CREATE INDEX idx_client_operateur ON Client(id_operateur);
CREATE INDEX idx_compte_client ON Compte(id_client);
CREATE INDEX idx_operation_source ON Operation(id_compte_source);
CREATE INDEX idx_operation_destination ON Operation(id_compte_destination);
CREATE INDEX idx_bareme_type ON BaremeFrais(id_type_operation);

INSERT INTO statut_operation (libelle) VALUES ('en attente');
INSERT INTO statut_operation (libelle) VALUES ('reussie');
INSERT INTO statut_operation (libelle) VALUES ('annulee');

INSERT INTO TypeOperation (libelle) VALUES ('depot');
INSERT INTO TypeOperation (libelle) VALUES ('retrait');
INSERT INTO TypeOperation (libelle) VALUES ('transfert');

INSERT INTO Operateur (nom) VALUES 
('Orange'),
('Yas'),
('Airtel');

INSERT INTO Prefixe (id_operateur, code) VALUES
(1, '032'),
(1, '037'),

(2, '038'),
(2, '034'),

(3, '033');

INSERT INTO BaremeFrais (id_type_operation, montant_min, montant_max, valeur_frais, id_operateur) VALUES
(2, 100,       1000,     50, 2),
(2, 1001,      5000,     50, 2),
(2, 5001,      10000,    100, 2),
(2, 10001,     25000,    200, 2),
(2, 25001,     50000,    400, 2),
(2, 50001,     100000,   800, 2),
(2, 100001,    250000,   1500, 2),
(2, 250001,    500000,   1500, 2),
(2, 500001,    1000000,  2500, 2),
(2, 1000001,   2000000,  3000, 2);

INSERT INTO BaremeFrais (id_type_operation, montant_min, montant_max, valeur_frais, id_operateur) VALUES
(3, 100,       1000,     50, 2),
(3, 1001,      5000,     50, 2),
(3, 5001,      10000,    100, 2),
(3, 10001,     25000,    200, 2),
(3, 25001,     50000,    400, 2),
(3, 50001,     100000,   800, 2),
(3, 100001,    250000,   1500, 2),
(3, 250001,    500000,   1500, 2),
(3, 500001,    1000000,  2500, 2),
(3, 1000001,   2000000,  3000, 2);

CREATE TABLE CommissionInterOperateur (
    id_commission    INTEGER PRIMARY KEY AUTOINCREMENT,
    id_operateur     INTEGER NOT NULL,   -- l'opérateur qui applique cette commission
    pourcentage      REAL NOT NULL,      -- ex: 0.02 pour 2%
    FOREIGN KEY (id_operateur) REFERENCES Operateur(id_operateur)
);

CREATE TABLE ReductionMemeOperateur (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_expediteur INTEGER NOT NULL,
    pourcentage REAL NOT NULL,
    FOREIGN KEY (id_expediteur) REFERENCES Operateur(id_operateur)
);

INSERT INTO ReductionMemeOperateur (id_expediteur, pourcentage) VALUES 
(2, 10); 

INSERT INTO CommissionInterOperateur (id_operateur, pourcentage) VALUES
(1, 0.02),  
(2, 0.015),  
(3, 0.01);

CREATE VIEW vue_gains_operations AS
SELECT
    Operation.id_operation,
    Operation.montant,
    Operation.frais_appliques,
    Operation.date_operation,
    TypeOperation.libelle AS type_operation,
    OperateurSource.id_operateur   AS id_operateur_source,
    OperateurDest.id_operateur     AS id_operateur_destination,
    (
        SELECT bf.valeur_frais
        FROM BaremeFrais bf
        WHERE bf.id_operateur = OperateurSource.id_operateur
          AND bf.id_type_operation = Operation.id_type_operation
          AND Operation.montant BETWEEN bf.montant_min AND bf.montant_max
        LIMIT 1
    ) AS frais_base
FROM Operation
JOIN TypeOperation   ON TypeOperation.id_type_operation = Operation.id_type_operation
JOIN statut_operation ON statut_operation.id_statut = Operation.id_statut
JOIN Compte  AS CompteSource ON CompteSource.id_compte = Operation.id_compte_source
JOIN Client  AS ClientSource ON ClientSource.id_client = CompteSource.id_client
JOIN Operateur AS OperateurSource ON OperateurSource.id_operateur = ClientSource.id_operateur
LEFT JOIN Compte  AS CompteDest ON CompteDest.id_compte = Operation.id_compte_destination
LEFT JOIN Client  AS ClientDest ON ClientDest.id_client = CompteDest.id_client
LEFT JOIN Operateur AS OperateurDest ON OperateurDest.id_operateur = ClientDest.id_operateur
WHERE statut_operation.libelle = 'reussie'
  AND TypeOperation.libelle != 'depot';

CREATE VIEW vue_transferts_inter_operateurs AS
SELECT
    Operation.id_operation,
    Operation.montant,
    Operation.date_operation,
    OperateurSource.id_operateur AS id_operateur_source,
    OperateurSource.nom AS nom_operateur_source,
    OperateurDest.id_operateur AS id_operateur_dest,
    OperateurDest.nom AS nom_operateur_dest
FROM Operation
JOIN TypeOperation ON TypeOperation.id_type_operation = Operation.id_type_operation
JOIN statut_operation ON statut_operation.id_statut = Operation.id_statut
JOIN Compte AS CompteSource ON CompteSource.id_compte = Operation.id_compte_source
JOIN Client AS ClientSource ON ClientSource.id_client = CompteSource.id_client
JOIN Operateur AS OperateurSource ON OperateurSource.id_operateur = ClientSource.id_operateur
JOIN Operateur AS OperateurDest ON OperateurDest.id_operateur = Operation.id_operateur_destination
WHERE statut_operation.libelle = 'reussie'
  AND TypeOperation.libelle = 'transfert'
  AND OperateurSource.id_operateur != OperateurDest.id_operateur;

CREATE VIEW vue_historique_operations AS
SELECT
    o.id_operation,
    o.id_type_operation,
    o.id_statut,
    o.montant,
    o.frais_appliques,
    o.date_operation,
    o.id_compte_source,
    o.id_compte_destination,

    t.libelle AS type_libelle,
    s.libelle AS statut_libelle,

    cs.numero_telephone AS numero_source,
    cs.nom AS nom_source,
    cds.numero_telephone AS numero_destination,
    cds.nom AS nom_destination,

    os.id_operateur AS id_operateur_source,
    os.nom AS nom_operateur_source,

    od.id_operateur AS id_operateur_destination,
    od.nom AS nom_operateur_destination

FROM Operation o

JOIN TypeOperation t
ON t.id_type_operation = o.id_type_operation

JOIN statut_operation s
ON s.id_statut = o.id_statut

LEFT JOIN Compte cps
ON cps.id_compte = o.id_compte_source

LEFT JOIN Client cs
ON cs.id_client = cps.id_client

LEFT JOIN Operateur os
ON os.id_operateur = cs.id_operateur

LEFT JOIN Compte cpd
ON cpd.id_compte = o.id_compte_destination

LEFT JOIN Client cds
ON cds.id_client = cpd.id_client

LEFT JOIN Operateur od
ON od.id_operateur = cds.id_operateur;


CREATE VIEW vue_commissions_inter_operateurs AS
SELECT

    os.id_operateur AS id_operateur_source,
    os.nom AS nom_operateur_source,

    od.id_operateur AS id_operateur_destination,
    od.nom AS nom_operateur_dest,

    o.id_operation,
    o.date_operation,
    o.montant,
    o.frais_appliques

FROM Operation o

JOIN Compte cps
ON cps.id_compte=o.id_compte_source

JOIN Client cs
ON cs.id_client=cps.id_client

JOIN Operateur os
ON os.id_operateur=cs.id_operateur

JOIN Compte cpd
ON cpd.id_compte=o.id_compte_destination

JOIN Client cd
ON cd.id_client=cpd.id_client

JOIN Operateur od
ON od.id_operateur=cd.id_operateur

WHERE
    o.id_type_operation=3
AND o.id_statut=2
AND os.id_operateur<>od.id_operateur;


CREATE VIEW vue_operations_gains AS
SELECT

    o.id_operation,
    o.id_type_operation,
    t.libelle AS type_operation,

    o.id_compte_source,
    o.id_compte_destination,

    os.id_operateur AS id_operateur_source,
    od.id_operateur AS id_operateur_destination,

    o.montant,
    o.frais_appliques,
    o.date_operation

FROM Operation o

JOIN TypeOperation t
ON t.id_type_operation=o.id_type_operation

LEFT JOIN Compte cps
ON cps.id_compte=o.id_compte_source

LEFT JOIN Client cs
ON cs.id_client=cps.id_client

LEFT JOIN Operateur os
ON os.id_operateur=cs.id_operateur

LEFT JOIN Compte cpd
ON cpd.id_compte=o.id_compte_destination

LEFT JOIN Client cd
ON cd.id_client=cpd.id_client

LEFT JOIN Operateur od
ON od.id_operateur=cd.id_operateur

WHERE
    o.id_statut=2
AND o.id_type_operation<>1;