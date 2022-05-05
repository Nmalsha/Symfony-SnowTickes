{% extends 'base.html.twig' %}

{% block body %}
{% if
app.request.attributes.get('_route')=='trick_gallery' %}
<section class="tickes">
<h4>Add gallery images</h4>


  <div class="card trickcard_style">
    {{ form_start(formGallary) }}
    <div class="form_row">
      <input type="text" name="trickId" value="{{trick.id}}" />
    </div>
    {% endif %}
    <div class="form_row">
      <button type="submit" class="btn btn-success">
       Ajouter le image
      </button>
      {{ form_end(formGallary) }}

    </div>
  </div>
</section>

{% endblock %}
