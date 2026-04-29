@extends('frontend.layouts.master')

@section('contents')
    <section id="dashboard">
        <div class="container">
            <div class="row">
                <div class="col-xl-3 col-lg-4">
                    @include('frontend.dashboard.sidebar')
                </div>

                <div class="col-lg-9">

                    <div class="row">
                        <!-- TODO:: estaciones y vigencia -->
                    </div>
                    <div class="dashboard_content" data-content-reference="price-table-content-maosa-api">
                        <div class="no-price-list">
                            <div class="precio-layout" style="background:#FFFFFF;">
                                <div class="pl-header" style="border-bottom:1px solid #CCCCCC;">
                                    <h2 style="font-size:16px; color:#333333;">
                                        PUBLICO EN GENERAL
                                    </h2>

                                    <p class="tipo" style="font-size:14px; color:#333333;">
                                        PROSPECTO
                                    </p>

                                    <p class="fecha" style="font-size:13px; color:#333333;">
                                        martes, 28 de abril de 2026
                                    </p>
                                </div>
                                <table style="font-size:18px;">
                                    <thead>
                                    <tr>

                                        <th
                                            style="text-align:left; background:#4A4A4A; color:#FFFFFF; border:1px solid #CCCCCC; font-size:16px;">
                                            Terminal</th>



                                        <th
                                            style="text-align:left; background:#4A4A4A; color:#FFFFFF; border:1px solid #CCCCCC; font-size:16px;">
                                            Calidad</th>


                                        <th
                                            style="text-align:left; background:#4A4A4A; color:#FFFFFF; border:1px solid #CCCCCC; font-size:16px;">
                                            Flete</th>


                                        <th class="prod-h" style="background-color:#00B050; border:1px solid #CCCCCC; font-size:16px;">MAGNA
                                        </th>

                                        <th class="prod-h" style="background-color:#FF0000; border:1px solid #CCCCCC; font-size:16px;">PREMIUM
                                        </th>

                                        <th class="prod-h" style="background-color:#000000; border:1px solid #CCCCCC; font-size:16px;">DIESEL
                                        </th>

                                    </tr>
                                    </thead>
                                    <tbody>

                                    <tr style="background:#FFFFFF;">

                                        <td style="border:1px solid #CCCCCC;">ALTAMIRA</td>



                                        <td style="border:1px solid #CCCCCC;">RDP</td>


                                        <td class="num" style="border:1px solid #CCCCCC;">$ -</td>



                                        <td class="num" style="border:1px solid #CCCCCC;">$ 25.7196</td>


                                        <td class="num" style="border:1px solid #CCCCCC;">$ 26.8273</td>


                                        <td class="num" style="border:1px solid #CCCCCC;">$ 28.0991</td>

                                    </tr>

                                    <tr style="background:#F9F9F9;">

                                        <td style="border:1px solid #CCCCCC;">ALTAMIRA</td>



                                        <td style="border:1px solid #CCCCCC;">ZM</td>


                                        <td class="num" style="border:1px solid #CCCCCC;">$ -</td>



                                        <td class="num" style="border:1px solid #CCCCCC;">$ 25.7396</td>


                                        <td class="num" style="border:1px solid #CCCCCC;">$ 27.0173</td>


                                        <td class="num" style="border:1px solid #CCCCCC;">$ 28.0991</td>

                                    </tr>

                                    <tr style="background:#FFFFFF;">

                                        <td style="border:1px solid #CCCCCC;">GLENCORE DOS BOCAS</td>



                                        <td style="border:1px solid #CCCCCC;">RDP</td>


                                        <td class="num" style="border:1px solid #CCCCCC;">$ -</td>



                                        <td class="num" style="border:1px solid #CCCCCC;">$ 26.1119</td>


                                        <td class="num" style="border:1px solid #CCCCCC;">$ 27.3550</td>


                                        <td class="num" style="border:1px solid #CCCCCC;">$ 29.3219</td>

                                    </tr>

                                    <tr style="background:#F9F9F9;">

                                        <td style="border:1px solid #CCCCCC;">GLENCORE MONTERRA</td>



                                        <td style="border:1px solid #CCCCCC;">RDP</td>


                                        <td class="num" style="border:1px solid #CCCCCC;">$ -</td>



                                        <td class="num" style="border:1px solid #CCCCCC;">$ 27.3637</td>


                                        <td class="num" style="border:1px solid #CCCCCC;">$ 26.3808</td>


                                        <td class="num" style="border:1px solid #CCCCCC;">$ 28.5037</td>

                                    </tr>

                                    <tr style="background:#FFFFFF;">

                                        <td style="border:1px solid #CCCCCC;">NVO. LAREDO 16%</td>



                                        <td style="border:1px solid #CCCCCC;">RDP</td>


                                        <td class="num" style="border:1px solid #CCCCCC;">$ -</td>



                                        <td class="num" style="border:1px solid #CCCCCC;">$ 24.7087</td>


                                        <td class="num" style="border:1px solid #CCCCCC;">$ 26.7638</td>


                                        <td class="num" style="border:1px solid #CCCCCC;">$ 27.6277</td>

                                    </tr>

                                    <tr style="background:#F9F9F9;">

                                        <td style="border:1px solid #CCCCCC;">NVO. LAREDO 8%</td>



                                        <td style="border:1px solid #CCCCCC;">RDP</td>


                                        <td class="num" style="border:1px solid #CCCCCC;">$ -</td>



                                        <td class="num" style="border:1px solid #CCCCCC;">$ 23.0454</td>


                                        <td class="num" style="border:1px solid #CCCCCC;">$ 24.9678</td>


                                        <td class="num" style="border:1px solid #CCCCCC;">$ 25.7562</td>

                                    </tr>

                                    <tr style="background:#FFFFFF;">

                                        <td style="border:1px solid #CCCCCC;">VALERO PUEBLA</td>



                                        <td style="border:1px solid #CCCCCC;">RDP</td>


                                        <td class="num" style="border:1px solid #CCCCCC;">$ -</td>



                                        <td class="num" style="border:1px solid #CCCCCC;">$ 25.9276</td>


                                        <td class="num" style="border:1px solid #CCCCCC;">$ 26.9228</td>


                                        <td class="num" style="border:1px solid #CCCCCC;">$ 28.1644</td>

                                    </tr>

                                    <tr style="background:#F9F9F9;">

                                        <td style="border:1px solid #CCCCCC;">VALERO SILOS TIZA</td>



                                        <td style="border:1px solid #CCCCCC;">RDP</td>


                                        <td class="num" style="border:1px solid #CCCCCC;">$ -</td>



                                        <td class="num" style="border:1px solid #CCCCCC;">$ 26.1991</td>


                                        <td class="num" style="border:1px solid #CCCCCC;">$ 27.6411</td>


                                        <td class="num" style="border:1px solid #CCCCCC;">$ 28.4591</td>

                                    </tr>

                                    <tr style="background:#FFFFFF;">

                                        <td style="border:1px solid #CCCCCC;">VALERO SILOS TIZA</td>



                                        <td style="border:1px solid #CCCCCC;">ZM</td>


                                        <td class="num" style="border:1px solid #CCCCCC;">$ -</td>



                                        <td class="num" style="border:1px solid #CCCCCC;">$ 26.1508</td>


                                        <td class="num" style="border:1px solid #CCCCCC;">$ 28.3550</td>


                                        <td class="num" style="border:1px solid #CCCCCC;">$ 28.4591</td>

                                    </tr>

                                    <tr style="background:#F9F9F9;">

                                        <td style="border:1px solid #CCCCCC;">VALERO TIZAYUCA</td>



                                        <td style="border:1px solid #CCCCCC;">RDP</td>


                                        <td class="num" style="border:1px solid #CCCCCC;">$ -</td>



                                        <td class="num" style="border:1px solid #CCCCCC;">$ 25.9195</td>


                                        <td class="num" style="border:1px solid #CCCCCC;">$ 27.1496</td>


                                        <td class="num" style="border:1px solid #CCCCCC;">$ 28.1926</td>

                                    </tr>

                                    <tr style="background:#FFFFFF;">

                                        <td style="border:1px solid #CCCCCC;">VALERO TIZAYUCA</td>



                                        <td style="border:1px solid #CCCCCC;">ZM</td>


                                        <td class="num" style="border:1px solid #CCCCCC;">$ -</td>



                                        <td class="num" style="border:1px solid #CCCCCC;">$ 25.8711</td>


                                        <td class="num" style="border:1px solid #CCCCCC;">$ 28.0406</td>


                                        <td class="num" style="border:1px solid #CCCCCC;">$ 28.1926</td>

                                    </tr>

                                    <tr style="background:#F9F9F9;">

                                        <td style="border:1px solid #CCCCCC;">VALERO VERACRUZ</td>



                                        <td style="border:1px solid #CCCCCC;">RDP</td>


                                        <td class="num" style="border:1px solid #CCCCCC;">$ -</td>



                                        <td class="num" style="border:1px solid #CCCCCC;">$ 25.2585</td>


                                        <td class="num" style="border:1px solid #CCCCCC;">$ 26.3632</td>


                                        <td class="num" style="border:1px solid #CCCCCC;">$ 27.9982</td>

                                    </tr>

                                    </tbody>
                                </table>
                                <div class="pl-footer">

                                    <div class="pl-notas" style="background:#d9d4ce; color:#333333; font-size:16px;">

                                        <div>Vigencia de la oferta hasta las 23:59 horas de la fecha indicada.</div>

                                        <div>Todos los costos incluyen IVA. Precio por litro. No aplicable a facturación.</div>

                                        <div>Producto sujeto a disponibilidad de la terminal.</div>

                                        <div>Precio puede cambiar sin aviso durante el día.</div>

                                        <div>No incluye flete y/o entrega a última milla.</div>

                                        <div>En caso de requerir transporte, consultar con su ejecutiva.</div>

                                        <div>Calidad RDP NO aplica a las zonas metroplitanas.</div>

                                        <div>ZMVM Zona Metropolitana de Valle de México.</div>

                                        <div>ZMM Zona Metropolitana de Monterrey.</div>

                                        <div>ZMG Zona Metrolitana de Guadalajara.</div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
