{% extends 'base.html.twig' %}

{% block title %}My Learning Dashboard{% endblock %}

{% block content %}
<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none">
            <h2 class="page-title">My Enrolled Courses</h2>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            {% if enrollments is empty %}
                <div class="empty">
                    <p class="empty-title">You haven't enrolled in any courses yet.</p>
                    <div class="empty-action">
                        <a href="{{ path('app_course_index') }}" class="btn btn-primary">Browse Courses</a>
                    </div>
                </div>
            {% else %}
                <div class="row row-cards">
                    {% for enrollment in enrollments %}
                        <div class="col-md-6 col-lg-4">
                            <div class="card">
                                <div class="card-body">
                                    <h3 class="card-title">{{ enrollment.course.title }}</h3>
                                    <p class="text-muted">Enrolled on: {{ enrollment.enrolledAt|date('M d, Y') }}</p>
                                    <a href="{{ path('app_course_show', {id: enrollment.course.id}) }}" class="btn btn-outline-primary w-100">Continue Learning</a>
                                </div>
                            </div>
                        </div>
                    {% endfor %}
                </div>
            {% endif %}
        </div>
    </div>
</div>
{% endblock %}