<script type="text/javascript">
    function imprimer_page(){
    window.print();
    }
</script>

<style>
    .example-wrapper { margin: 1em auto; max-width: 800px; width: 95%; font: 18px/1.5 sans-serif; }
    .example-wrapper code { background: #F5F5F5; padding: 2px 6px; }

    @media print {
      .label {
        display: inline-block;
        width: 10.19cm;
        height: 5.11cm;
        border: 1px solid black;
        padding-top: 10px;
        padding-left: 10px;
        margin: 0;
        box-sizing: border-box;
        border-radius: 25px; 
        font-size: 15px;
        line-height: 0.7;
      }
      
      .labels-container {
        display: grid;
        grid-template-columns: repeat(2, 10.19cm);
        grid-template-rows: repeat(5, 5.11cm);
        gap: 0.0cm 0.4cm;
        margin: 0cm 0.4cm;
      }

      .notImpressed {
        display: none;
      }
    }


</style>

    <div class="example-wrapper notImpressed">
        <form action={{ path('app_admin_stickers_par_date') }} method="GET">
            <input name="date" type="date" value="{{date | date('Y-m-d') }}">
            <input type="submit">
        </form>

    </div>


    <div class="container">
    <a href="{{ path('admin') }}" class="btn btn-secondary my-3 notImpressed">Back to Admin</a>

        <div>
            <form class="notImpressed ">
                <input id="impression" name="impression" type="button" onclick="imprimer_page()" value="Imprimer cette page" />
            </form>
        </div>
        
        <div class="labels-container mt-5">
                <div class="label">
                    <p><strong>Nom:</strong> {{ meetingInfos.holder }}</p>
                    <p><strong>Hôtel:</strong> 
                        {% for infosTransfer in meetingInfos.transfers %}
                            {{ infosTransfer.toArrival}}
                        {% endfor %}
                    </p>
                    <p><strong>Reunion de Bienvenue &amp; d'Information</strong></p>
                    <div class="row">
                        <div class="col-6">
                    <p><strong>Jour:</strong> Demain</p>
                    <p><strong>Lieu de rencontre:</strong> {{ (meetingInfos.meetingPoint.name is defined) ? meetingInfos.meetingPoint.name : ""}}</p>


                            <p><strong>Votre représentant:</strong> {{ meetingInfos.staff }}</p>
                            <p><strong>Whatsapp:</strong> +1 809 723 0945</p>
                        </div>
                        <div class="col-6">
                        {% if meetingInfos.staff.phoneNumber is defined and  meetingInfos.staff.phoneNumber != null %}    <img src="{{ qr_code_url('wa.me/' ~ meetingInfos.staff.phoneNumber) }}" /> {% endif %}
                        </div>
                    </div>
                </div>
            <!-- Ajoutez les autres étiquettes ici -->
        </div>

    </div>











