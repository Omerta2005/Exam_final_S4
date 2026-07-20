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
    montant               REAL NOT NULL,
    frais_appliques       REAL NOT NULL DEFAULT 0,
    date_operation        TEXT NOT NULL DEFAULT (datetime('now')),
    id_statut              INTEGER NOT NULL DEFAULT 1,
    FOREIGN KEY (id_type_operation) REFERENCES TypeOperation(id_type_operation),
    FOREIGN KEY (id_compte_source) REFERENCES Compte(id_compte),
    FOREIGN KEY (id_compte_destination) REFERENCES Compte(id_compte),
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

INSERT INTO Operateur (nom) VALUES ('Mobile Money Sim');
INSERT INTO Prefixe (id_operateur, code) VALUES (1, '033');
INSERT INTO Prefixe (id_operateur, code) VALUES (1, '037');

INSERT INTO BaremeFrais (id_type_operation, montant_min, montant_max, valeur_frais, id_operateur) VALUES
(2, 100,       1000,     50, 1),
(2, 1001,      5000,     50, 1),
(2, 5001,      10000,    100, 1),
(2, 10001,     25000,    200, 1),
(2, 25001,     50000,    400, 1),
(2, 50001,     100000,   800, 1),
(2, 100001,    250000,   1500, 1),
(2, 250001,    500000,   1500, 1),
(2, 500001,    1000000,  2500, 1),
(2, 1000001,   2000000,  3000, 1);

INSERT INTO BaremeFrais (id_type_operation, montant_min, montant_max, valeur_frais, id_operateur) VALUES
(3, 100,       1000,     50, 1),
(3, 1001,      5000,     50, 1),
(3, 5001,      10000,    100, 1),
(3, 10001,     25000,    200, 1),
(3, 25001,     50000,    400, 1),
(3, 50001,     100000,   800, 1),
(3, 100001,    250000,   1500, 1),
(3, 250001,    500000,   1500, 1),
(3, 500001,    1000000,  2500, 1),
(3, 1000001,   2000000,  3000, 1);

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