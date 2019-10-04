# tad-core
TAD backend implementation


# Documentation
TAD deve avere una proprietà che definisce se è lavorabile o meno e un metodo che controlla questa proprietà (set o get in fase di parsing)

Quando lo istanzio se ho tutti i dati che mi aspetto setto workable true
Posso settare i parametri che mi servono anche successivamente, ovvero dopo aver creato istanza, ma ad ogni inserimento verifico e se non ho tutte le cose apposto non metto workable
per eseguire il runner che chiama mood o fa altro per gestire il singolo tad prima verifica che il tad sia workable, altrimenti restituisce errori (missing etc etc)

van verificati i missing e i wrong:
Replica req i su res i, non gli interessa cosa sia, al max controllo nella collection se assegnare un valore o meno a i nel caso non sia arrivata da client

t non può essere missing, a non può essere missing
t deve essere valorizzato: una stringa non vuota, a deve essere valorizzato: una stringa non vuota

funzione che controlla se req t e req a sono valorizzate, se si è workable, altrimenti no



TAD
- workable
- req
 - i
 - t
 - a
 - d
- res
 - i
 - t
 - a
 - d

il tadmanager.worker quando lavora un tad:

    controlla se workable
    se non workable
        res i = req i
        se req t missing valorizza res t missing
        se req a missing valorizza res a missing
        res d non viene valorizzato (unset)
    se workable
        imposta res i = req i
        verifica type e se errore (non previsto / non permesso) imposta res t e workable false?
        verifica action e se errore (non previsto / non permesso) imposta res a e workable false?
        lo lavora (tramite mood o altro) e imposta res d

in fase di istanza, ovvero quando il tadmanager istanzia un nuovo tad da pushare nella tadcollection il tad si popola con un array [ itad ]
oppure possono essere richiamati i metodi specifici delle singole proprietà.

miotad = new TAD($data)

miotad->setT(t)
miotad->setA(a)
miotad->setD(d)

i metodi set e construct alla fine chiameranno un metodo checkWorkable che solo se tutto è in ordine imposta workable su true
