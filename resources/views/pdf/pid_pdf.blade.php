<html>
    <head>
        <style>
        table {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        td, th {
            border: 1px solid #111;
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) {
            background-color: #dddddd;
        }
        </style>
    </head>
    <body>
        <h3 style="font-size:26px; margin-bottom:20px; text-align:center; border-bottom:1px solid #111;">PAIEMENT IMMÉDIAT DIRECT</h3>
        <h3 style="margin:0;font-size: 18px;">Dr <span id="detail_doctor_name">{{ $doctor['name'] }}</span></h3>
        <p style="margin:0;">Service Imagerie :<span id="detail_service_name">{{ $doctor['treatment']['name'] }}, </span></p>
        <p style="margin:0;">
            <span id="detail_hospital_name">
            @foreach ($hospitals as $hospital)
                {{ $hospital['name'] }}, 
            @endforeach
            </span>
        </p>
        <p style="margin:0;"><span id="detail_hospital_state"></span></p>
        <p style="margin:0;"><span id="detail_hospital_location"></span></p>
        <div style="width:60%; margin-top:30px; margin-left:40%;">
            <p style="margin:0;">Matricule :<span id="detail_patient_number">{{ $doctor_pid['patient_number'] }}</span></p>
            <p style="margin:0;">Patient : <span id="detail_patient_name">NOM Prénom</span></p>
        </div>

        <h5 class="mt-3" style="text-decoration:underline">Prestations de soins de santé</h5>
        <p style="margin:0;">N° <span></span></p>
        <p style="margin:0;">du 27.02.2023</p>
        <p style="margin:0;">N° ID 20230227196605306156700337788 </p>
        <table>
            <thead>
                <tr>
                    <th>Code Acte</th>
                    <th>Libellé / Objet</th>
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
                    <td>C20</td>
                    <td>Consultation</td>
                    <td>1</td>
                    <td>{{ $doctor_pid['part_statutaire'] }}</td>
                    <td>€</td>
                    <td>{{ $doctor_pid['recouvrement'] }}</td>
                    <td>€</td>
                </tr>
                <tr>
                    <td>C20</td>
                    <td>Consultation</td>
                    <td>1</td>
                    <td>{{ $doctor_pid['recouvrement'] }}</td>
                    <td>€</td>
                    <td>{{ $doctor_pid['recouvrement'] }}</td>
                    <td>€</td>
                </tr>
                <tr>
                    <td></td>
                    <td><b>Total:<b></td>
                    <td></td>
                    <td>{{ $doctor_pid['paye'] }}</td>
                    <td>€</td>
                    <td>{{ $doctor_pid['recouvrement'] }}</td>
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
