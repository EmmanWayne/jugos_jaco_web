<?php

namespace App\Enums;

enum MunicipalityEnum: string
{
    // Municipios de Atlántida
    case LA_CEIBA = 'La Ceiba|Atlántida';
    case EL_PORVENIR_ATLANTIDA = 'El Porvenir|Atlántida';
    case TELA = 'Tela|Atlántida';
    case JUTIAPA = 'Jutiapa|Atlántida';
    case LA_MASICA = 'La Masica|Atlántida';
    case SAN_FRANCISCO_ATLANTIDA = 'San Francisco|Atlántida';
    case ARIZONA = 'Arizona|Atlántida';
    case ESPARTA = 'Esparta|Atlántida';

        // Municipios de Choluteca
    case CHOLUTECA = 'Choluteca|Choluteca';
    case APACILAGUA = 'Apacilagua|Choluteca';
    case CONCEPCION_DE_MARIA = 'Concepción de María|Choluteca';
    case DUYURE = 'Duyure|Choluteca';
    case EL_CORPUS = 'El Corpus|Choluteca';
    case EL_TRIUNFO = 'El Triunfo|Choluteca';
    case MARCOVIA = 'Marcovia|Choluteca';
    case MOROLICA = 'Morolica|Choluteca';
    case NAMASIGUE = 'Namasigüe|Choluteca';
    case OROCUINA = 'Orocuina|Choluteca';
    case PESPIRE = 'Pespire|Choluteca';
    case SAN_ANTONIO_DE_FLORES_CHOLUTECA = 'San Antonio de Flores|Choluteca';
    case SAN_ISIDRO_CHOLUTECA = 'San Isidro|Choluteca';
    case SAN_JOSE_CHOLUTECA = 'San José|Choluteca';
    case SAN_MARCOS_DE_COLON = 'San Marcos de Colón|Choluteca';
    case SANTA_ANA_DE_YUSGUARE = 'Santa Ana de Yusguare|Choluteca';

        // Municipios de Colón
    case TRUJILLO = 'Trujillo|Colón';
    case BALFATE = 'Balfate|Colón';
    case IRIONA = 'Iriona|Colón';
    case LIMON = 'Limón|Colón';
    case SABA = 'Sabá|Colón';
    case SANTA_FE_COLON = 'Santa Fe|Colón';
    case SANTA_ROSA_DE_AGUAN = 'Santa Rosa de Aguán|Colón';
    case SONAGUERA = 'Sonaguera|Colón';
    case TOCOA = 'Tocoa|Colón';
    case BONITO_ORIENTAL = 'Bonito Oriental|Colón';

        // Municipios de Comayagua
    case COMAYAGUA = 'Comayagua|Comayagua';
    case AJUTERIQUE = 'Ajuterique|Comayagua';
    case EL_ROSARIO = 'El Rosario|Comayagua';
    case ESQUIAS = 'Esquías|Comayagua';
    case HUMUYA = 'Humuya|Comayagua';
    case LA_LIBERTAD_COMAYAGUA = 'La Libertad|Comayagua';
    case LAMANI = 'Lamaní|Comayagua';
    case LA_TRINIDAD = 'La Trinidad|Comayagua';
    case LEJAMANI = 'Lejamaní|Comayagua';
    case MEAMBAR = 'Meámbar|Comayagua';
    case MINAS_DE_ORO = 'Minas de Oro|Comayagua';
    case OJOS_DE_AGUA = 'Ojos de Agua|Comayagua';
    case SAN_JERONIMO_COMAYAGUA = 'San Jerónimo|Comayagua';
    case SAN_JOSE_DE_COMAYAGUA = 'San José de Comayagua|Comayagua';
    case SAN_JOSE_DEL_POTRERO = 'San José del Potrero|Comayagua';
    case SAN_LUIS_COMAYAGUA = 'San Luis|Comayagua';
    case SAN_SEBASTIAN_COMAYAGUA = 'San Sebastián|Comayagua';
    case SIGUATEPEQUE = 'Siguatepeque|Comayagua';
    case VILLA_DE_SAN_ANTONIO = 'Villa de San Antonio|Comayagua';
    case LAS_LAJAS = 'Las Lajas|Comayagua';
    case TAULABE = 'Taulabé|Comayagua';

        // Municipios de Copán
    case SANTA_ROSA_DE_COPAN = 'Santa Rosa de Copán|Copán';
    case CABANAS = 'Cabañas|Copán';
    case CONCEPCION_COPAN = 'Concepción|Copán';
    case COPAN_RUINAS = 'Copán Ruinas|Copán';
    case CORQUIN = 'Corquín|Copán';
    case CUCUYAGUA = 'Cucuyagua|Copán';
    case DOLORES_COPAN = 'Dolores|Copán';
    case DULCE_NOMBRE = 'Dulce Nombre|Copán';
    case EL_PARAISO_COPAN = 'El Paraíso|Copán';
    case FLORIDA_COPAN = 'Florida|Copán';
    case LA_JIGUA = 'La Jigua|Copán';
    case LA_UNION_COPAN = 'La Unión|Copán';
    case NUEVA_ARCADIA = 'Nueva Arcadia|Copán';
    case SAN_AGUSTIN = 'San Agustín|Copán';
    case SAN_ANTONIO_COPAN = 'San Antonio|Copán';
    case SAN_JERONIMO_COPAN = 'San Jerónimo|Copán';
    case SAN_JOSE_COPAN = 'San José|Copán';
    case SAN_JUAN_DE_OPOA = 'San Juan de Opoa|Copán';
    case SAN_NICOLAS_COPAN = 'San Nicolás|Copán';
    case SAN_PEDRO_COPAN = 'San Pedro|Copán';
    case SANTA_RITA_COPAN = 'Santa Rita|Copán';
    case TRINIDAD_DE_COPAN = 'Trinidad de Copán|Copán';
    case VERACRUZ_COPAN = 'Veracruz|Copán';

        // Municipios de Cortés
    case SAN_PEDRO_SULA = 'San Pedro Sula|Cortés';
    case CHOLOMA = 'Choloma|Cortés';
    case OMOA = 'Omoa|Cortés';
    case PIMIENTA = 'Pimienta|Cortés';
    case POTRERILLOS_CORTES = 'Potrerillos|Cortés';
    case PUERTO_CORTES = 'Puerto Cortés|Cortés';
    case SAN_ANTONIO_DE_CORTES = 'San Antonio de Cortés|Cortés';
    case SAN_FRANCISCO_DE_YOJOA = 'San Francisco de Yojoa|Cortés';
    case SAN_MANUEL = 'San Manuel|Cortés';
    case SANTA_CRUZ_DE_YOJOA = 'Santa Cruz de Yojoa|Cortés';
    case VILLANUEVA = 'Villanueva|Cortés';
    case LA_LIMA = 'La Lima|Cortés';

        // Municipios de El Paraíso
    case YUSCARAN = 'Yuscarán|El Paraíso';
    case ALAUCA = 'Alauca|El Paraíso';
    case DANLI = 'Danlí|El Paraíso';
    case EL_PARAISO_EP = 'El Paraíso|El Paraíso';
    case GUINOPE = 'Güinope|El Paraíso';
    case JACALEAPA = 'Jacaleapa|El Paraíso';
    case LIURE = 'Liure|El Paraíso';
    case MOROCELI = 'Morocelí|El Paraíso';
    case OROPOLI = 'Oropolí|El Paraíso';
    case POTRERILLOS_EP = 'Potrerillos|El Paraíso';
    case SAN_ANTONIO_DE_FLORES_EP = 'San Antonio de Flores|El Paraíso';
    case SAN_LUCAS = 'San Lucas|El Paraíso';
    case SAN_MATIAS = 'San Matías|El Paraíso';
    case SOLEDAD = 'Soledad|El Paraíso';
    case TEUPASENTI = 'Teupasenti|El Paraíso';
    case TEXIGUAT = 'Texiguat|El Paraíso';
    case VADO_ANCHO = 'Vado Ancho|El Paraíso';
    case YAUYUPE = 'Yauyupe|El Paraíso';
    case TROJES = 'Trojes|El Paraíso';

        // Municipios de Francisco Morazán
    case DISTRITO_CENTRAL = 'Distrito Central|Francisco Morazán';
    case ALUBAREN = 'Alubarén|Francisco Morazán';
    case CEDROS = 'Cedros|Francisco Morazán';
    case CURAREN = 'Curarén|Francisco Morazán';
    case EL_PORVENIR_FM = 'El Porvenir|Francisco Morazán';
    case GUAIMACA = 'Guaimaca|Francisco Morazán';
    case LA_LIBERTAD_FM = 'La Libertad|Francisco Morazán';
    case LA_VENTA = 'La Venta|Francisco Morazán';
    case LEPATERIQUE = 'Lepaterique|Francisco Morazán';
    case MARAITA = 'Maraita|Francisco Morazán';
    case MARALE = 'Marale|Francisco Morazán';
    case NUEVA_ARMENIA = 'Nueva Armenia|Francisco Morazán';
    case OJOJONA = 'Ojojona|Francisco Morazán';
    case ORICA = 'Orica|Francisco Morazán';
    case REITOCA = 'Reitoca|Francisco Morazán';
    case SABANAGRANDE = 'Sabanagrande|Francisco Morazán';
    case SAN_ANTONIO_DE_ORIENTE = 'San Antonio de Oriente|Francisco Morazán';
    case SAN_BUENAVENTURA = 'San Buenaventura|Francisco Morazán';
    case SAN_IGNACIO = 'San Ignacio|Francisco Morazán';
    case SAN_JUAN_DE_FLORES = 'San Juan de Flores|Francisco Morazán';
    case SAN_MIGUELITO = 'San Miguelito|Francisco Morazán';
    case SANTA_ANA_FM = 'Santa Ana|Francisco Morazán';
    case SANTA_LUCIA_FM = 'Santa Lucía|Francisco Morazán';
    case TALANGA = 'Talanga|Francisco Morazán';
    case TATUMBLA = 'Tatumbla|Francisco Morazán';
    case VALLE_DE_ANGELES = 'Valle de Ángeles|Francisco Morazán';
    case VILLA_DE_SAN_FRANCISCO = 'Villa de San Francisco|Francisco Morazán';
    case VALLECILLO = 'Vallecillo|Francisco Morazán';

        // Municipios de Gracias a Dios
    case PUERTO_LEMPIRA = 'Puerto Lempira|Gracias a Dios';
    case BRUS_LAGUNA = 'Brus Laguna|Gracias a Dios';
    case AHUAS = 'Ahuas|Gracias a Dios';
    case JUAN_FRANCISCO_BULNES = 'Juan Francisco Bulnes|Gracias a Dios';
    case VILLEDA_MORALES = 'Villeda Morales|Gracias a Dios';
    case WAMPUSIRPE = 'Wampusirpe|Gracias a Dios';

        // Municipios de Intibucá
    case LA_ESPERANZA = 'La Esperanza|Intibucá';
    case CAMASCA = 'Camasca|Intibucá';
    case COLOMONCAGUA = 'Colomoncagua|Intibucá';
    case CONCEPCION_INTIBUCA = 'Concepción|Intibucá';
    case DOLORES_INTIBUCA = 'Dolores|Intibucá';
    case INTIBUCA = 'Intibucá|Intibucá';
    case JESUS_DE_OTORO = 'Jesús de Otoro|Intibucá';
    case MAGDALENA = 'Magdalena|Intibucá';
    case MASAGUARA = 'Masaguara|Intibucá';
    case SAN_ANTONIO_INTIBUCA = 'San Antonio|Intibucá';
    case SAN_ISIDRO_INTIBUCA = 'San Isidro|Intibucá';
    case SAN_JUAN_INTIBUCA = 'San Juan|Intibucá';
    case SAN_MARCOS_DE_LA_SIERRA = 'San Marcos de la Sierra|Intibucá';
    case SAN_MIGUEL_GUANCAPLA = 'San Miguel Guancapla|Intibucá';
    case SANTA_LUCIA_INTIBUCA = 'Santa Lucía|Intibucá';
    case YAMARANGUILA = 'Yamaranguila|Intibucá';
    case SAN_FRANCISCO_DE_OPALACA = 'San Francisco de Opalaca|Intibucá';

        // Municipios de Islas de la Bahía
    case ROATAN = 'Roatán|Islas de la Bahía';
    case GUANAJA = 'Guanaja|Islas de la Bahía';
    case JOSE_SANTOS_GUARDIOLA = 'José Santos Guardiola|Islas de la Bahía';
    case UTILA = 'Utila|Islas de la Bahía';

        // Municipios de La Paz
    case LA_PAZ = 'La Paz|La Paz';
    case AGUANQUETERIQUE = 'Aguanqueterique|La Paz';
    case CABANAS_LP = 'Cabañas|La Paz';
    case CANE = 'Cane|La Paz';
    case CHINACLA = 'Chinacla|La Paz';
    case GUAJIQUIRO = 'Guajiquiro|La Paz';
    case LAUTERIQUE = 'Lauterique|La Paz';
    case MARCALA = 'Marcala|La Paz';
    case MERCEDES_DE_ORIENTE = 'Mercedes de Oriente|La Paz';
    case OPATORO = 'Opatoro|La Paz';
    case SAN_ANTONIO_DEL_NORTE = 'San Antonio del Norte|La Paz';
    case SAN_JOSE_LP = 'San José|La Paz';
    case SAN_JUAN_LP = 'San Juan|La Paz';
    case SAN_PEDRO_DE_TUTULE = 'San Pedro de Tutule|La Paz';
    case SANTA_ANA_LP = 'Santa Ana|La Paz';
    case SANTA_ELENA = 'Santa Elena|La Paz';
    case SANTA_MARIA = 'Santa María|La Paz';
    case SANTIAGO_DE_PURINGLA = 'Santiago de Puringla|La Paz';
    case YARULA = 'Yarula|La Paz';

        // Municipios de Lempira
    case GRACIAS = 'Gracias|Lempira';
    case BELEN = 'Belén|Lempira';
    case CANDELARIA = 'Candelaria|Lempira';
    case COLOLACA = 'Cololaca|Lempira';
    case ERANDIQUE = 'Erandique|Lempira';
    case GUALCINCE = 'Gualcince|Lempira';
    case GUARITA = 'Guarita|Lempira';
    case LA_CAMPA = 'La Campa|Lempira';
    case LA_IGUALA = 'La Iguala|Lempira';
    case LAS_FLORES = 'Las Flores|Lempira';
    case LA_UNION_LEMPIRA = 'La Unión|Lempira';
    case LA_VIRTUD = 'La Virtud|Lempira';
    case LEPAERA = 'Lepaera|Lempira';
    case MAPULACA = 'Mapulaca|Lempira';
    case PIRAERA = 'Piraera|Lempira';
    case SAN_ANDRES = 'San Andrés|Lempira';
    case SAN_FRANCISCO_LEMPIRA = 'San Francisco|Lempira';
    case SAN_JUAN_GUARITA = 'San Juan Guarita|Lempira';
    case SAN_MANUEL_COLOHETE = 'San Manuel Colohete|Lempira';
    case SAN_RAFAEL_LEMPIRA = 'San Rafael|Lempira';
    case SAN_SEBASTIAN_LEMPIRA = 'San Sebastián|Lempira';
    case SANTA_CRUZ_LEMPIRA = 'Santa Cruz|Lempira';
    case TALGUA = 'Talgua|Lempira';
    case TAMBLA = 'Tambla|Lempira';
    case TOMALÁ = 'Tomalá|Lempira';
    case VALLADOLID = 'Valladolid|Lempira';
    case VIRGINIA = 'Virginia|Lempira';
    case SAN_MARCOS_DE_CAIQUIN = 'San Marcos de Caiquín|Lempira';

        // Municipios de Ocotepeque
    case OCOTEPEQUE = 'Ocotepeque|Ocotepeque';
    case BELEN_GUALCHO = 'Belén Gualcho|Ocotepeque';
    case CONCEPCION_OCOTEPEQUE = 'Concepción|Ocotepeque';
    case DOLORES_MERENDON = 'Dolores Merendón|Ocotepeque';
    case FRATERNIDAD = 'Fraternidad|Ocotepeque';
    case LA_ENCARNACION = 'La Encarnación|Ocotepeque';
    case LA_LABOR = 'La Labor|Ocotepeque';
    case LUCERNA = 'Lucerna|Ocotepeque';
    case MERCEDES_OCOTEPEQUE = 'Mercedes|Ocotepeque';
    case SAN_FERNANDO_OCOTEPEQUE = 'San Fernando|Ocotepeque';
    case SAN_FRANCISCO_DEL_VALLE = 'San Francisco del Valle|Ocotepeque';
    case SAN_JORGE = 'San Jorge|Ocotepeque';
    case SAN_MARCOS_OCOTEPEQUE = 'San Marcos|Ocotepeque';
    case SANTA_FE_OCOTEPEQUE = 'Santa Fe|Ocotepeque';
    case SENSENTI = 'Sensenti|Ocotepeque';
    case SINUAPA = 'Sinuapa|Ocotepeque';

        // Municipios de Olancho
    case JUTICALPA = 'Juticalpa|Olancho';
    case CAMPAMENTO = 'Campamento|Olancho';
    case CATACAMAS = 'Catacamas|Olancho';
    case CONCORDIA_OLANCHO = 'Concordia|Olancho';
    case DULCE_NOMBRE_DE_CULMI = 'Dulce Nombre de Culmí|Olancho';
    case EL_ROSARIO_OLANCHO = 'El Rosario|Olancho';
    case ESQUIPULAS_DEL_NORTE = 'Esquipulas del Norte|Olancho';
    case GUALACO = 'Gualaco|Olancho';
    case GUARIZAMA = 'Guarizama|Olancho';
    case GUATA = 'Guata|Olancho';
    case GUAYAPE = 'Guayape|Olancho';
    case JANO = 'Jano|Olancho';
    case LA_UNION_OLANCHO = 'La Unión|Olancho';
    case MANGULILE = 'Mangulile|Olancho';
    case MANTO = 'Manto|Olancho';
    case SALAMA = 'Salamá|Olancho';
    case SAN_ESTEBAN = 'San Esteban|Olancho';
    case SAN_FRANCISCO_DE_BECERRA = 'San Francisco de Becerra|Olancho';
    case SAN_FRANCISCO_DE_LA_PAZ = 'San Francisco de la Paz|Olancho';
    case SANTA_MARIA_DEL_REAL = 'Santa María del Real|Olancho';
    case SILCA = 'Silca|Olancho';
    case YOCON = 'Yocón|Olancho';
    case PATUCA = 'Patuca|Olancho';

        // Municipios de Santa Bárbara
    case SANTA_BARBARA = 'Santa Bárbara|Santa Bárbara';
    case ARADA = 'Arada|Santa Bárbara';
    case ATIMA = 'Atima|Santa Bárbara';
    case AZACUALPA = 'Azacualpa|Santa Bárbara';
    case CEGUACA = 'Ceguaca|Santa Bárbara';
    case CONCEPCION_DEL_NORTE = 'Concepción del Norte|Santa Bárbara';
    case CONCEPCION_DEL_SUR = 'Concepción del Sur|Santa Bárbara';
    case CHINDA = 'Chinda|Santa Bárbara';
    case EL_NISPERO = 'El Níspero|Santa Bárbara';
    case GUALALA = 'Gualala|Santa Bárbara';
    case ILAMA = 'Ilama|Santa Bárbara';
    case MACUELIZO = 'Macuelizo|Santa Bárbara';
    case NARANJITO = 'Naranjito|Santa Bárbara';
    case NUEVO_CELILAC = 'Nuevo Celilac|Santa Bárbara';
    case PETOA = 'Petoa|Santa Bárbara';
    case PROTECCION = 'Protección|Santa Bárbara';
    case QUIMISTAN = 'Quimistán|Santa Bárbara';
    case SAN_FRANCISCO_DE_OJUERA = 'San Francisco de Ojuera|Santa Bárbara';
    case SAN_JOSE_DE_LAS_COLINAS = 'San José de las Colinas|Santa Bárbara';
    case SAN_LUIS = 'San Luis|Santa Bárbara';
    case SAN_MARCOS_SB = 'San Marcos|Santa Bárbara';
    case SAN_NICOLAS_SB = 'San Nicolás|Santa Bárbara';
    case SAN_PEDRO_ZACAPA = 'San Pedro Zacapa|Santa Bárbara';
    case SANTA_RITA_SB = 'Santa Rita|Santa Bárbara';
    case SAN_VICENTE_CENTENARIO = 'San Vicente Centenario|Santa Bárbara';
    case TRINIDAD_SB = 'Trinidad|Santa Bárbara';
    case LAS_VEGAS = 'Las Vegas|Santa Bárbara';
    case NUEVA_FRONTERA = 'Nueva Frontera|Santa Bárbara';

        // Municipios de Valle
    case NACAOME = 'Nacaome|Valle';
    case ALIANZA = 'Alianza|Valle';
    case AMAPALA = 'Amapala|Valle';
    case ARAMECINA = 'Aramecina|Valle';
    case CARIDAD = 'Caridad|Valle';
    case GOASCORAN = 'Goascorán|Valle';
    case LANGUE = 'Langue|Valle';
    case SAN_FRANCISCO_DE_CORAY = 'San Francisco de Coray|Valle';
    case SAN_LORENZO = 'San Lorenzo|Valle';

        // Municipios de Yoro
    case YORO = 'Yoro|Yoro';
    case ARENAL = 'Arenal|Yoro';
    case EL_NEGRITO = 'El Negrito|Yoro';
    case EL_PROGRESO = 'El Progreso|Yoro';
    case JOCON = 'Jocón|Yoro';
    case MORAZAN_YORO = 'Morazán|Yoro';
    case OLANCHITO = 'Olanchito|Yoro';
    case SANTA_RITA_YORO = 'Santa Rita|Yoro';
    case SULACO = 'Sulaco|Yoro';
    case VICTORIA = 'Victoria|Yoro';
    case YORITO = 'Yorito|Yoro';

    public static function getByDepartment(DepartmentEnum $department): array
    {
        $municipalities = match ($department) {
            DepartmentEnum::ATLANTIDA => [
                self::LA_CEIBA->value,
                self::EL_PORVENIR_ATLANTIDA->value,
                self::TELA->value,
                self::JUTIAPA->value,
                self::LA_MASICA->value,
                self::SAN_FRANCISCO_ATLANTIDA->value,
                self::ARIZONA->value,
                self::ESPARTA->value,
            ],

            DepartmentEnum::CHOLUTECA => [
                self::CHOLUTECA->value,
                self::APACILAGUA->value,
                self::CONCEPCION_DE_MARIA->value,
                self::DUYURE->value,
                self::EL_CORPUS->value,
                self::EL_TRIUNFO->value,
                self::MARCOVIA->value,
                self::MOROLICA->value,
                self::NAMASIGUE->value,
                self::OROCUINA->value,
                self::PESPIRE->value,
                self::SAN_ANTONIO_DE_FLORES_CHOLUTECA->value,
                self::SAN_ISIDRO_CHOLUTECA->value,
                self::SAN_JOSE_CHOLUTECA->value,
                self::SAN_MARCOS_DE_COLON->value,
                self::SANTA_ANA_DE_YUSGUARE->value,
            ],

            DepartmentEnum::COLON => [
                self::TRUJILLO->value,
                self::BALFATE->value,
                self::IRIONA->value,
                self::LIMON->value,
                self::SABA->value,
                self::SANTA_FE_COLON->value,
                self::SANTA_ROSA_DE_AGUAN->value,
                self::SONAGUERA->value,
                self::TOCOA->value,
                self::BONITO_ORIENTAL->value,
            ],

            DepartmentEnum::COMAYAGUA => [
                self::COMAYAGUA->value,
                self::AJUTERIQUE->value,
                self::EL_ROSARIO->value,
                self::ESQUIAS->value,
                self::HUMUYA->value,
                self::LA_LIBERTAD_COMAYAGUA->value,
                self::LAMANI->value,
                self::LA_TRINIDAD->value,
                self::LEJAMANI->value,
                self::MEAMBAR->value,
                self::MINAS_DE_ORO->value,
                self::OJOS_DE_AGUA->value,
                self::SAN_JERONIMO_COMAYAGUA->value,
                self::SAN_JOSE_DE_COMAYAGUA->value,
                self::SAN_JOSE_DEL_POTRERO->value,
                self::SAN_LUIS_COMAYAGUA->value,
                self::SAN_SEBASTIAN_COMAYAGUA->value,
                self::SIGUATEPEQUE->value,
                self::VILLA_DE_SAN_ANTONIO->value,
                self::LAS_LAJAS->value,
                self::TAULABE->value,
            ],

            DepartmentEnum::COPAN => [
                self::SANTA_ROSA_DE_COPAN->value,
                self::CABANAS->value,
                self::CONCEPCION_COPAN->value,
                self::COPAN_RUINAS->value,
                self::CORQUIN->value,
                self::CUCUYAGUA->value,
                self::DOLORES_COPAN->value,
                self::DULCE_NOMBRE->value,
                self::EL_PARAISO_COPAN->value,
                self::FLORIDA_COPAN->value,
                self::LA_JIGUA->value,
                self::LA_UNION_COPAN->value,
                self::NUEVA_ARCADIA->value,
                self::SAN_AGUSTIN->value,
                self::SAN_ANTONIO_COPAN->value,
                self::SAN_JERONIMO_COPAN->value,
                self::SAN_JOSE_COPAN->value,
                self::SAN_JUAN_DE_OPOA->value,
                self::SAN_NICOLAS_COPAN->value,
                self::SAN_PEDRO_COPAN->value,
                self::SANTA_RITA_COPAN->value,
                self::TRINIDAD_DE_COPAN->value,
                self::VERACRUZ_COPAN->value,
            ],

            DepartmentEnum::CORTES => [
                self::SAN_PEDRO_SULA->value,
                self::CHOLOMA->value,
                self::OMOA->value,
                self::PIMIENTA->value,
                self::POTRERILLOS_CORTES->value,
                self::PUERTO_CORTES->value,
                self::SAN_ANTONIO_DE_CORTES->value,
                self::SAN_FRANCISCO_DE_YOJOA->value,
                self::SAN_MANUEL->value,
                self::SANTA_CRUZ_DE_YOJOA->value,
                self::VILLANUEVA->value,
                self::LA_LIMA->value,
            ],

            DepartmentEnum::EL_PARAISO => [
                self::YUSCARAN->value,
                self::ALAUCA->value,
                self::DANLI->value,
                self::EL_PARAISO_EP->value,
                self::GUINOPE->value,
                self::JACALEAPA->value,
                self::LIURE->value,
                self::MOROCELI->value,
                self::OROPOLI->value,
                self::POTRERILLOS_EP->value,
                self::SAN_ANTONIO_DE_FLORES_EP->value,
                self::SAN_LUCAS->value,
                self::SAN_MATIAS->value,
                self::SOLEDAD->value,
                self::TEUPASENTI->value,
                self::TEXIGUAT->value,
                self::VADO_ANCHO->value,
                self::YAUYUPE->value,
                self::TROJES->value,
            ],

            DepartmentEnum::FRANCISCO_MORAZAN => [
                self::DISTRITO_CENTRAL->value,
                self::ALUBAREN->value,
                self::CEDROS->value,
                self::CURAREN->value,
                self::EL_PORVENIR_FM->value,
                self::GUAIMACA->value,
                self::LA_LIBERTAD_FM->value,
                self::LA_VENTA->value,
                self::LEPATERIQUE->value,
                self::MARAITA->value,
                self::MARALE->value,
                self::NUEVA_ARMENIA->value,
                self::OJOJONA->value,
                self::ORICA->value,
                self::REITOCA->value,
                self::SABANAGRANDE->value,
                self::SAN_ANTONIO_DE_ORIENTE->value,
                self::SAN_BUENAVENTURA->value,
                self::SAN_IGNACIO->value,
                self::SAN_JUAN_DE_FLORES->value,
                self::SAN_MIGUELITO->value,
                self::SANTA_ANA_FM->value,
                self::SANTA_LUCIA_FM->value,
                self::TALANGA->value,
                self::TATUMBLA->value,
                self::VALLE_DE_ANGELES->value,
                self::VILLA_DE_SAN_FRANCISCO->value,
                self::VALLECILLO->value,
            ],

            DepartmentEnum::GRACIAS_A_DIOS => [
                self::PUERTO_LEMPIRA->value,
                self::BRUS_LAGUNA->value,
                self::AHUAS->value,
                self::JUAN_FRANCISCO_BULNES->value,
                self::VILLEDA_MORALES->value,
                self::WAMPUSIRPE->value,
            ],

            DepartmentEnum::INTIBUCA => [
                self::LA_ESPERANZA->value,
                self::CAMASCA->value,
                self::COLOMONCAGUA->value,
                self::CONCEPCION_INTIBUCA->value,
                self::DOLORES_INTIBUCA->value,
                self::INTIBUCA->value,
                self::JESUS_DE_OTORO->value,
                self::MAGDALENA->value,
                self::MASAGUARA->value,
                self::SAN_ANTONIO_INTIBUCA->value,
                self::SAN_ISIDRO_INTIBUCA->value,
                self::SAN_JUAN_INTIBUCA->value,
                self::SAN_MARCOS_DE_LA_SIERRA->value,
                self::SAN_MIGUEL_GUANCAPLA->value,
                self::SANTA_LUCIA_INTIBUCA->value,
                self::YAMARANGUILA->value,
                self::SAN_FRANCISCO_DE_OPALACA->value,
            ],

            DepartmentEnum::ISLAS_DE_LA_BAHIA => [
                self::ROATAN->value,
                self::GUANAJA->value,
                self::JOSE_SANTOS_GUARDIOLA->value,
                self::UTILA->value,
            ],

            DepartmentEnum::LA_PAZ => [
                self::LA_PAZ->value,
                self::AGUANQUETERIQUE->value,
                self::CABANAS_LP->value,
                self::CANE->value,
                self::CHINACLA->value,
                self::GUAJIQUIRO->value,
                self::LAUTERIQUE->value,
                self::MARCALA->value,
                self::MERCEDES_DE_ORIENTE->value,
                self::OPATORO->value,
                self::SAN_ANTONIO_DEL_NORTE->value,
                self::SAN_JOSE_LP->value,
                self::SAN_JUAN_LP->value,
                self::SAN_PEDRO_DE_TUTULE->value,
                self::SANTA_ANA_LP->value,
                self::SANTA_ELENA->value,
                self::SANTA_MARIA->value,
                self::SANTIAGO_DE_PURINGLA->value,
                self::YARULA->value,
            ],

            DepartmentEnum::LEMPIRA => [
                self::GRACIAS->value,
                self::BELEN->value,
                self::CANDELARIA->value,
                self::COLOLACA->value,
                self::ERANDIQUE->value,
                self::GUALCINCE->value,
                self::GUARITA->value,
                self::LA_CAMPA->value,
                self::LA_IGUALA->value,
                self::LAS_FLORES->value,
                self::LA_UNION_LEMPIRA->value,
                self::LA_VIRTUD->value,
                self::LEPAERA->value,
                self::MAPULACA->value,
                self::PIRAERA->value,
                self::SAN_ANDRES->value,
                self::SAN_FRANCISCO_LEMPIRA->value,
                self::SAN_JUAN_GUARITA->value,
                self::SAN_MANUEL_COLOHETE->value,
                self::SAN_RAFAEL_LEMPIRA->value,
                self::SAN_SEBASTIAN_LEMPIRA->value,
                self::SANTA_CRUZ_LEMPIRA->value,
                self::TALGUA->value,
                self::TAMBLA->value,
                self::TOMALÁ->value,
                self::VALLADOLID->value,
                self::VIRGINIA->value,
                self::SAN_MARCOS_DE_CAIQUIN->value,
            ],

            DepartmentEnum::OCOTEPEQUE => [
                self::OCOTEPEQUE->value,
                self::BELEN_GUALCHO->value,
                self::CONCEPCION_OCOTEPEQUE->value,
                self::DOLORES_MERENDON->value,
                self::FRATERNIDAD->value,
                self::LA_ENCARNACION->value,
                self::LA_LABOR->value,
                self::LUCERNA->value,
                self::MERCEDES_OCOTEPEQUE->value,
                self::SAN_FERNANDO_OCOTEPEQUE->value,
                self::SAN_FRANCISCO_DEL_VALLE->value,
                self::SAN_JORGE->value,
                self::SAN_MARCOS_OCOTEPEQUE->value,
                self::SANTA_FE_OCOTEPEQUE->value,
                self::SENSENTI->value,
                self::SINUAPA->value,
            ],

            DepartmentEnum::OLANCHO => [
                self::JUTICALPA->value,
                self::CAMPAMENTO->value,
                self::CATACAMAS->value,
                self::CONCORDIA_OLANCHO->value,
                self::DULCE_NOMBRE_DE_CULMI->value,
                self::EL_ROSARIO_OLANCHO->value,
                self::ESQUIPULAS_DEL_NORTE->value,
                self::GUALACO->value,
                self::GUARIZAMA->value,
                self::GUATA->value,
                self::GUAYAPE->value,
                self::JANO->value,
                self::LA_UNION_OLANCHO->value,
                self::MANGULILE->value,
                self::MANTO->value,
                self::SALAMA->value,
                self::SAN_ESTEBAN->value,
                self::SAN_FRANCISCO_DE_BECERRA->value,
                self::SAN_FRANCISCO_DE_LA_PAZ->value,
                self::SANTA_MARIA_DEL_REAL->value,
                self::SILCA->value,
                self::YOCON->value,
                self::PATUCA->value,
            ],

            DepartmentEnum::SANTA_BARBARA => [
                self::SANTA_BARBARA->value,
                self::ARADA->value,
                self::ATIMA->value,
                self::AZACUALPA->value,
                self::CEGUACA->value,
                self::CONCEPCION_DEL_NORTE->value,
                self::CONCEPCION_DEL_SUR->value,
                self::CHINDA->value,
                self::EL_NISPERO->value,
                self::GUALALA->value,
                self::ILAMA->value,
                self::MACUELIZO->value,
                self::NARANJITO->value,
                self::NUEVO_CELILAC->value,
                self::PETOA->value,
                self::PROTECCION->value,
                self::QUIMISTAN->value,
                self::SAN_FRANCISCO_DE_OJUERA->value,
                self::SAN_JOSE_DE_LAS_COLINAS->value,
                self::SAN_LUIS->value,
                self::SAN_MARCOS_SB->value,
                self::SAN_NICOLAS_SB->value,
                self::SAN_PEDRO_ZACAPA->value,
                self::SANTA_RITA_SB->value,
                self::SAN_VICENTE_CENTENARIO->value,
                self::TRINIDAD_SB->value,
                self::LAS_VEGAS->value,
                self::NUEVA_FRONTERA->value,
            ],

            DepartmentEnum::VALLE => [
                self::NACAOME->value,
                self::ALIANZA->value,
                self::AMAPALA->value,
                self::ARAMECINA->value,
                self::CARIDAD->value,
                self::GOASCORAN->value,
                self::LANGUE->value,
                self::SAN_FRANCISCO_DE_CORAY->value,
                self::SAN_LORENZO->value,
            ],

            DepartmentEnum::YORO => [
                self::YORO->value,
                self::ARENAL->value,
                self::EL_NEGRITO->value,
                self::EL_PROGRESO->value,
                self::JOCON->value,
                self::MORAZAN_YORO->value,
                self::OLANCHITO->value,
                self::SANTA_RITA_YORO->value,
                self::SULACO->value,
                self::VICTORIA->value,
                self::YORITO->value,
            ],

            default => [],
        };

        // Limpiamos los valores para mostrar solo el nombre del municipio
        return array_map(function ($value) {
            return explode('|', $value)[0];
        }, $municipalities);
    }
}
