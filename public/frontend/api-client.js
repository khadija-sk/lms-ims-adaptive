class IMSApiClient {
    constructor(baseUrl = '/api', tokenKey = 'auth_token') {
        this.baseUrl = baseUrl;
        this.tokenKey = tokenKey;
    }

    getToken() { return localStorage.getItem(this.tokenKey); }
    setToken(token) { localStorage.setItem(this.tokenKey, token); }
    clearToken() { localStorage.removeItem(this.tokenKey); }

    async request(endpoint, options = {}) {
        const url = `${this.baseUrl}${endpoint}`;
        const headers = { 'Content-Type': 'application/json', 'Accept': 'application/json', ...options.headers };
        const token = this.getToken();
        if (token) headers['Authorization'] = `Bearer ${token}`;
        const response = await fetch(url, { ...options, headers });
        const data = await response.json();
        if (!response.ok) throw { status: response.status, data, message: data.message || 'Erreur API' };
        return data;
    }

    async get(endpoint) { return this.request(endpoint, { method: 'GET' }); }
    async post(endpoint, body = {}) { return this.request(endpoint, { method: 'POST', body: JSON.stringify(body) }); }

    // ============ AUTHENTIFICATION ============
    async login(email, mot_de_passe) {
        const response = await this.post('/auth/login', { email, mot_de_passe });
        if (response.token) this.setToken(response.token);
        if (response.user) localStorage.setItem('user', JSON.stringify(response.user));
        return response;
    }

    async register(nom, prenom, email, mot_de_passe, role = 'etudiant') {
        return this.post('/auth/register', { nom, prenom, email, mot_de_passe, role });
    }

    async logout() {
        const response = await this.post('/auth/logout');
        this.clearToken();
        localStorage.removeItem('user');
        return response;
    }

    async getCurrentUser() { return this.get('/auth/user'); }

    // ============ COURS ============
    async getAllCourses() { return this.get('/cours'); }
    async getCourse(id) { return this.get(`/cours/${id}`); }
    async createCourse(titre, description, niveau, categorie) {
        return this.post('/cours', { titre, description, niveau, categorie });
    }
    async enrollCourse(id) { return this.post(`/cours/${id}/inscrire`); }
    async getCourseQuizzes(courseId) { return this.get(`/cours/${courseId}/quiz`); }

    // ============ QUIZ ============
    async getQuiz(id) { return this.get(`/quiz/${id}`); }
    async submitQuiz(quizId, reponses) { return this.post(`/quiz/${quizId}/soumettre`, { reponses }); }

    // ============ CERTIFICATS ============
    async generateCertificate(id_cours) { return this.post('/certificats', { id_cours }); }
    async getStudentCertificates(studentId) { return this.get(`/etudiant/${studentId}/certificats`); }
    async verifyCertificate(code) { return this.get(`/certificats/verifier/${code}`); }
    async downloadCertificate(id) { return this.get(`/certificats/${id}/telecharger`); }

    // ============ ANALYTICS ============
    async getStudentAnalytics(studentId) { return this.get(`/analytics/etudiant/${studentId}`); }
    async getCourseAnalytics(courseId) { return this.get(`/analytics/cours/${courseId}`); }
    async getGlobalAnalytics() { return this.get('/analytics/global'); }

    // ============ RECOMMANDATIONS ============
    async getRecommendations(studentId) { return this.get(`/recommend/${studentId}`); }
    async getIaRecommendations(studentId) { return this.get(`/ia/recommandations/${studentId}`); }
    async getSuggestions(studentId) { return this.get(`/ia/suggestions/${studentId}`); }
}

const apiClient = new IMSApiClient();