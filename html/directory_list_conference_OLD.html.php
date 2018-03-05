<!-- BEGIN: contenido_principal -->
<div id="attendee_list">
    <table>
        <tr>
            <td>
                {CONTENIDO_DESCRIPCION}

                <!-- BEGIN: listado_miembros -->
                <div id="lstResultsMemberDirectory">
                    <table width="100%">
                        <!-- BEGIN: tr -->
                        <tr>
                            <!-- BEGIN: item_miembro -->
                            <td>
                                <div class="itemMember">
                                    <table>
                                        <tr>
                                            <td class="headshot" style="overflow-x:hidden;vertical-align:middle;padding:5px;width:83px;text-align:center;">
                                                <img style="max-width:83px; max-height:90px;" src="{ITEM_MIEMBRO_IMAGEN}">
                                            </td>
                                            <td class="description">

                                                <h3 style="font-size:130%">{ITEM_MIEMBRO_BADGE1}</h3>
                                                <p>{ITEM_MIEMBRO_BADGE2}<br />
                                                    {ITEM_MIEMBRO_BADGE3}</p>


                                            </td>
                                        </tr>
                                    </table>

                                </div>
                            </td>
                            <!-- END: item_miembro -->
                        </tr>
                        <!-- END: tr -->
                    </table>
                </div>
                <!-- END: listado_miembros -->
                <!-- BEGIN: no_miembros -->
                <p style="text-align:center;">{STATIC_NO_MEMBERS_DIRECTORY}</p>
                <!-- END: no_miembros -->
                <!-- BEGIN: listado_noticias -->
                <ul id="lstNews">
                    <!-- BEGIN: item_noticia -->
                    <li>
                        <h3><a href="{ITEM_NOTICIA_URL}" class="titleNew">{ITEM_NOTICIA_TITULO}</a> {ITEM_NOTICIA_FECHA}</h3>
                        {ITEM_NOTICIA_DESCRIPCION}
                        <a href="{ITEM_NOTICIA_URL}" class="seeMore">(+)</a>
                    </li>
                    <!-- END: item_noticia -->
                </ul>
                <!-- END: listado_noticias -->
                {PAGINADOR}
            </td>
        </tr>
    </table>
</div>
<!-- END: contenido_principal -->