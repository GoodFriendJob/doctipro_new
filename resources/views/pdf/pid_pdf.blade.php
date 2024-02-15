<html>
    <head>
        <style>
        table {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 100%;
            border: 1px solid #aaa;
        }

        td, th {
            border: 1px solid #aaa;
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) {
            background-color: #ddd;
        }
        </style>
    </head>
    <body style="margin:30px 90px;">
        <h3 style="font-size:26px; margin-bottom:20px; text-align:center; border-bottom:1px solid #111;">PAIEMENT IMMÉDIAT DIRECT</h3>
        <h3 style="margin:0;font-size: 18px;">Dr <span id="detail_doctor_name">{{ $doctor['name'] }} {{ $doctor['last_name'] }}</span></h3>
        <p style="margin:0;">Service Imagerie : <b>{{ $doctor['category']['name'] }}</b></p>
        <p style="margin:0;">Hôpital : 
            <span id="detail_hospital_name">
            @foreach ($hospitals as $hospital)
                {{ $hospital['name'] }}, 
            @endforeach
            </span>
        </p>
        <p style="margin:0;">Adresse : <span id="doctor_address">{{ $doctor->user['address'] }}</span></p>
        <p style="margin:0;">Tél : <span id="doctor_phone">{{ $doctor->user['phone_code'] }} {{ $doctor->user['phone'] }}</span></p>
        <p style="margin:0;">E-mail : <span id="doctor_phone">{{ $doctor->user['email'] }}</span></p>
        <div style="width:40%; margin-left:60%;">
            <p style="margin:0;">Patient : <b>{{ $patient['name'] }} {{ $patient['last_name'] }}</b></p>
            <p style="margin:0">Matricule : 
                <b>{{ substr($patient['patient_id'], 0, 4).' '.substr($patient['patient_id'], 4, 2).' '.substr($patient['patient_id'], 6, 2).' '.substr($patient['patient_id'], 8, 3).' '.substr($patient['patient_id'], 11) }}</b> 
            </p>
            <p style="margin:0;">Adresse : <span>{{ $patient['address'] }}</span></p>
        </div>
        
        <h5 class="mt-3" style="text-decoration:underline">Prestations de soins de santé</h5>
        <p style="margin:0;">identifiant du émetteur de factures :<b>{{ $doctor_pid['biller_id'] }}</b></p>
        <p style="margin:0 0 30px;">Date :<b>{{ $doctor_pid['validation_date'] }}</b></p>
        <table>
            <thead>
                <tr>
                    <th>Code Acte</th>
                    <th>Libellé / Objet</th>
                    <th>Nbre</th>
                    <th colspan="2">Montant pris <br>
                        en charge par  <br>
                        l’AMM*
                    </th>
                    <th colspan="2">Participation  <br>
                        personnelle
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $doctor_pid['medical_code'] }}</td>
                    <td>Consultation</td>
                    <td>1</td>
                    <td>{{ $doctor_pid['totalPartStatutaire'] }}</td>
                    <td>€</td>
                    <td>{{ $doctor_pid['totalParticipationPersonelle'] }}</td>
                    <td>€</td>
                </tr>
                <tr>
                    <td></td>
                    <td><b>Total:<b></td>
                    <td></td>
                    <td colspan="3">{{ $doctor_pid['sommeTotale'] }}</td>
                    <td>€</td>
                </tr>
            </tbody>
        </table>
        <div style="paddin-right:30px; text-align:right; font-size:18px; margin-top:10px">
            <h5 style="margin:0;">Montant total dû : {{ $doctor_pid['recouvrement'] }} €</h5>            
            <h5 style="margin:0;">Montant total payé par l’AMM* : {{ $doctor_pid['paye'] }} €</h5> 
        </div>           
        <div style="margin-top:40px;">
            <div style="width:15%; float:left; text-align:right">
                <h1>!!!</h1>
            </div>
            <div style="width:70%; float:left; text-align:center">
                <p>Merci de ne pas envoyer ce document à votre caisse de <br>
                maladie.</p>
            </div>
            <div style="width:15%; float:left; text-align:left">
                <h1>!!!</h1>
            </div>
        </div>
        <div style="margin-top:40px; paddin-right:30px; text-align:right;">
            Signature et estampille du prestataire
        </div>

        <div style="margin-top:80px; font-size:12px; text-align:left;">
            *AMM = assurance maladie-maternité
        </div>
    </body>
</html>
